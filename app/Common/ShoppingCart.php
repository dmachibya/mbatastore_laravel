<?php

namespace App\Common;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

/**
 * Attach this Trait to a User (or other model) for easier read/writes on Addresses
 *
 * @author Munna Khan
 */
trait ShoppingCart
{
    /**
     * Get all carts of a user.
     *
     * @param boolean $from_api Whether the request is from API or not.
     * @return \Illuminate\Database\Eloquent\Collection|\App\Models\Cart[]
     */
    private function getShoppingCarts($from_api = false)
    {
        // Get all carts where customer_id is null
        $carts = Cart::whereNull('customer_id');

        if ($from_api) {
            $carts = $carts->where('ip_address', get_visitor_IP());
        } else {
            $carts = $carts->whereIn('id', cart_ids_from_cookie());
        }

        if (Auth::guard('customer')->check()) {
            $carts = $carts->orWhere('customer_id', Auth::guard('customer')->user()->id);
        } elseif (Auth::guard('api')->check()) {
            $carts = $carts->orWhere('customer_id', Auth::guard('api')->user()->id);
        }

        return $carts->get();
    }

    /**
     * Add given item to cart
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function addToCart(Request $request, $slug)
    {
        $slugsArray = json_decode($slug, true);

        // Check if the slugs parameter is a JSON string, if not, convert it to an array
        if (json_last_error() !== JSON_ERROR_NONE) {
            $slugsArray = [$slug];
        }

        $customer_id = $this->getCartOwnerId($request);

        $items = Inventory::whereIn('slug', $slugsArray)->get();

        if ($items->count() == 0) {
            return response()->json(trans('theme.item_not_available'), 404);
        }

        foreach ($items as $item) {
            // Check if the item is a downloadable one
            $downloadable = $item->product->downloadable;

            // Check the available stock limit
            if (!$downloadable && $request->quantity > $item->stock_quantity) {
                return response()->json(['message' => trans('api.item_max_stock')], 409);
            }

            // For POS
            if (
                is_incevio_package_loaded('pos') &&
                session('pos_cart_id') &&
                Auth::check() &&
                Auth::user()->isFromMerchant()
            ) {
                $old_cart = Cart::find(session('pos_cart_id'));
            } else { // For normal operations
                $old_cart = Cart::where('shop_id', $item->shop_id);

                // Check product type
                $old_cart = $old_cart->where('is_digital', $downloadable);

                // Exclude auction carts
                if (is_incevio_package_loaded('auction')) {
                    $old_cart = $old_cart->whereNull('auction_bid_id');
                }

                // Get the old cart of the customer if any
                if ($customer_id) {
                    $old_cart = $old_cart->where('customer_id', $customer_id)
                        ->orWhere(function ($q) {
                            $q->whereNull('customer_id')
                                ->whereIn('id', cart_ids_from_cookie());
                        });
                } else {
                    $old_cart = $old_cart->whereNull('customer_id')
                        ->whereIn('id', cart_ids_from_cookie());
                }

                $old_cart = $old_cart->first();
            }

            // Check if the item is already in the cart
            if ($old_cart instanceof Cart) {
                $item_in_cart = DB::table('cart_items')->where('cart_id', $old_cart->id)
                    ->where('inventory_id', $item->id)->first();

                // Item already in cart
                if ($item_in_cart) {
                    return response()->json(['cart_id' => $item_in_cart->cart_id], 444);
                }
            }

            $qtt = $request->quantity ?? $item->min_order_quantity;

            if (!$item->auctionable && is_incevio_package_loaded('wholesale')) {
                $unit_price = get_wholesale_unit_price($item, $qtt);
            } else {
                $unit_price = $item->current_sale_price();
            }

            // Instantiate new cart if old cart not found for the shop and customer
            $cart = $old_cart ?? new Cart;

            $cart->item_count = $old_cart ? ($old_cart->item_count + 1) : 1;
            $cart->quantity = $old_cart ? ($old_cart->quantity + $qtt) : $qtt;
            $cart->shop_id = $item->shop_id;
            $cart->customer_id = $customer_id;
            $cart->is_digital = $downloadable ?? 0;
            $cart->shipping_zone_id = $request->shippingZoneId;

            $cart->ship_to = $request->shipTo ?? $cart->ship_to;
            $cart->ship_to_country_id = $request->shipToCountryId ?? $cart->ship_to_country_id;
            $cart->ship_to_state_id = $request->shipToStateId ?? $cart->ship_to_state_id;

            // Shipping rate
            $cart->shipping_rate_id = null;

            if (!$cart->is_digital && !is_null($request->shippingRateId)) {
                $cart->shipping_rate_id = $request->shippingRateId;
            }

            $cart->handling = $cart->get_handling_cost();
            $cart->total = $old_cart ? ($old_cart->total + ($qtt * $unit_price)) : ($qtt * $unit_price);

            // Set shipping weight
            // If any item doesn't have shipping_weight, set null to cart shipping_weight
            if (!$cart->is_digital) {
                $cart->shipping_weight = $old_cart
                    ? $old_cart->shipping_weight + $item->shipping_weight
                    : $item->shipping_weight;
            } else {
                $cart->shipping_weight = null;
            }

            // Set taxes
            if ($cart->shipping_zone_id) {
                $cart->taxrate = optional($cart->shippingZone->tax)->taxrate;
                $cart->taxes = $cart->get_tax_amount();
            }

            $cart->grand_total = $cart->calculate_grand_total();
            $cart->save();

            // Check if cart id doesn't exist then add to array
            if (!$this->isCartInCookie($cart->id)) {
                $cartIds[] = $cart->id;
            }

            // Makes item_description field
            $attributes = implode(' - ', $item->attributeValues->pluck('value')->toArray());

            // Prepare pivot data
            $cart_item_pivot_data = [];
            $cart_item_pivot_data[$item->id] = [
                'inventory_id' => $item->id,
                'item_description' => "{$item->sku}: {$item->title} - {$attributes} - {$item->condition}",
                'quantity' => $qtt,
                'unit_price' => $unit_price,
            ];

            // Save cart items into pivot
            if (!empty($cart_item_pivot_data)) {
                $cart->inventories()->syncWithoutDetaching($cart_item_pivot_data);
            }
        }

        // Push the card ids to cookie if not exist
        if (!empty($cartIds)) {
            $this->pushCartIdsToCookie($cartIds);
        }

        return response()->json([$cart->toArray(), 'total_items_added' => $items->count()], 200);
    }

    /**
     * Remove item from the cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function removeFromCart(Request $request, string $slug)
    {
        $cart_id = $request->cart;

        // For POS cart
        if (!$cart_id && is_incevio_package_loaded('pos') && session('pos_cart_id')) {
            $cart_id = session('pos_cart_id');
        }

        $cart = Cart::findOrFail($cart_id);

        $slugs = json_decode($slug, true) ?: [$slug];
        $inventories = Inventory::whereIn('slug', $slugs)->get();

        $cart->inventories()->wherePivotIn('inventory_id', $inventories->pluck('id'))->detach();

        // Update cart
        $cart->total = $cart->inventories->sum(function ($item) {
            return $item->pivot->quantity * $item->pivot->unit_price;
        });

        $cart->grand_total = $cart->calculate_grand_total();
        $cart->item_count = $cart->inventories->count();
        $cart->quantity = $cart->inventories->sum('pivot.quantity');

        $cart->save();

        return response()->json([
            'cart' => $cart->toArray(),
            'total_items_removed' => $inventories->count(),
        ], 200);
    }

    /**
     * Get the payable amount of give carts or all
     *
     * @param Cart $carts
     * @return numeric | null
     */
    public function getTotalPayable($carts = null)
    {
        if (!$carts) {
            $carts = $this->getShoppingCarts();
        } elseif ($carts instanceof Cart) {
            return $carts->calculate_grand_total();
        }

        $payable = 0;

        foreach ($carts as $cart) {
            $payable += $cart->calculate_grand_total();
        }

        return $payable > 0 ? $payable : null;
    }

    /**
     * Create a new order from the cart
     *
     * @param Request $request
     * @param \App\Models\Cart $cart
     *
     * @return \App\Models\Order
     */
    private function saveOrderFromCart(Request $request, Cart $cart)
    {
        // Save the order
        $order = new Order;
        $order->fill(
            array_merge($cart->toArray(), [
                'customer_id' => $this->getCartOwnerId($request, $cart),
                'payment_method_id' => $request->payment_method_id ?? $cart->payment_method_id,
                'grand_total' => $cart->calculate_grand_total(),
                'order_number' => get_formated_order_number($cart->shop_id),
                'carrier_id' => $cart->carrier() ? $cart->carrier->id : null,
                'shipping_address' => $request->shipping_address ?? $cart->shipping_address,
                'billing_address' => $request->shipping_address ?? $cart->shipping_address,
                'email' => $request->email ?? $cart->email,
                'customer_phone_number' => $request->phone,
                'buyer_note' => $request->buyer_note,
                'device_id' => $request->device_id ?? $cart->device_id,
                'fulfilment_type' => $request->fulfilment_type ?? Order::FULFILMENT_TYPE_DELIVER,
                'warehouse_id' => ($request->fulfilment_type == Order::FULFILMENT_TYPE_PICKUP) ? $request->warehouse_id : null,
            ])
        )->save();

        // If the order is a pickup order enable free shipping for the cart
        $cart->setFulfilmentType($order->pickup() ? Order::FULFILMENT_TYPE_PICKUP : Order::FULFILMENT_TYPE_DELIVER);

        if ($request->has('prescription')) {
            $file = $request->file('prescription');

            // Request from API
            if ($request->is('api/*')) {
                $file = create_file_from_base64($request->get('prescription'));
            }

            $order->saveAttachments($file);
        }

        // This has to be after save the order
        if ($payment_instruction = $order->manualPaymentInstructions()) {
            $order->forceFill(['payment_instruction' => $payment_instruction])->save();
        }

        // Add order item into pivot table
        $order_items = [];
        foreach ($cart->inventories as $item) {
            // \Log::info($item);
            // if (is_incevio_package_loaded('pos') && session('pos_cart_id') == $cart->id) {
            $t_item_id = $item->id;
            // } else {
            //     $t_item_id = $item->inventory_id;
            // }

            $order_items[$t_item_id] = [
                'order_id' => $order->id,
                'inventory_id' => $item->id,
                'item_description' => $item->pivot->item_description,
                'quantity' => $item->pivot->quantity,
                'unit_price' => $item->pivot->unit_price,
                'created_at' => $item->pivot->created_at,
                'updated_at' => $item->pivot->updated_at,
            ];

            // Add credit reward amount 
            if (is_incevio_package_loaded('wallet') && is_wallet_credit_reward_enabled()) {
                $order_items[$t_item_id]['credit_back_amount'] = ($item->reward_percentage / 100) * $item->pivot->unit_price;
            }

            if (is_incevio_package_loaded('affiliate') && Session::has('affiliate_marketer_id')) {
                $affiliate_link = $item->affiliateLinks()
                    ->where('affiliate_id', Session::get('affiliate_marketer_id'))
                    ->first();

                if ($item->hasAffiliateCommission()) {
                    $affiliate_link->increment('order_count', $item->pivot->quantity);
                    $affiliate_link->createCommission($order->id, $item);
                }
            }
        }

        DB::table('order_items')->insert($order_items);

        foreach ($order->inventories as $item) {
            $item->increment('sold_quantity', $item->pivot->quantity);

            // Sync up the inventory. Decrease the stock of the order items from the listing
            if (!$cart->is_digital) {
                $item->decrement('stock_quantity', $item->pivot->quantity);
            }
        }

        // Reduce the coupon in use
        if ($order->coupon_id) {
            $coupon = Coupon::find($order->coupon_id);

            if ($coupon) {
                $coupon->decrement('quantity');
            }
        }

        // Delete the cart
        $cart->forceDelete();

        return $order;
    }

    /**
     * Revert order to cart
     *
     * @param \App\Models\Order $Order
     *
     * @return \App\Models\Cart|null
     */
    private function moveAllItemsToCartAgain($order, $revert = false, $cart = null)
    {
        if (!$order instanceof Order) {
            $order = Order::find($order);
        }

        if (!$order) {
            return;
        }

        // Set time
        $now = Carbon::now();

        // Fill the cart data
        $data = [
            'shop_id' => $order->shop_id,
            'customer_id' => $order->customer_id,
            'ship_to' => $order->ship_to,
            'shipping_zone_id' => $order->shipping_zone_id,
            'shipping_rate_id' => $order->shipping_rate_id,
            'packaging_id' => $order->packaging_id,
            'is_digital' => $order->is_digital,
            'item_count' => $order->item_count,
            'quantity' => $order->quantity,
            'taxrate' => $order->taxrate,
            'shipping_weight' => $order->shipping_weight,
            'total' => $order->total,
            'shipping' => $order->shipping,
            'packaging' => $order->packaging,
            'handling' => $order->handling,
            'taxes' => $order->taxes,
            'grand_total' => $order->grand_total,
            'email' => $order->email,
            'created_at' => $revert ? $order->created_at : $now,
            'updated_at' => $revert ? $order->updated_at : $now,
        ];

        // Keep the old cart id
        if ($cart) {
            $data = array_merge(['id' => $cart], $data);
        }

        // Save the cart
        $cart = Cart::forceCreate($data);

        // Add order item into cart pivot table
        $cart_items = [];
        $quantity = 0;
        $shipping_weight = 0;
        $total = 0;

        foreach ($order->inventories as $item) {
            // Skip if the item is out of stock
            if (!$item->stock_quantity > 0) {
                Session::flash('warning', trans('messages.some_item_out_of_stock'));
                continue;
            }

            // Set qtt after checking availability
            $item_qtt = $item->stock_quantity >= $item->pivot->quantity ?
                $item->pivot->quantity : $item->stock_quantity;

            $shipping_weight += $item->shipping_weight;
            $quantity += $item_qtt;

            // Get current updated price
            if (is_incevio_package_loaded('wholesale')) {
                $unit_price = get_wholesale_unit_price($item, $item_qtt);
            } else {
                $unit_price = $item->current_sale_price();
            }

            $total += $item_qtt * $unit_price;

            $cart_items[] = [
                'cart_id' => $cart->id,
                'inventory_id' => $item->pivot->inventory_id,
                'item_description' => $item->pivot->item_description,
                'quantity' => $item_qtt,
                'unit_price' => $unit_price,
                'created_at' => $revert ? $item->pivot->created_at : $now,
                'updated_at' => $revert ? $item->pivot->created_at : $now,
            ];

            // Sync up the inventory. Increase the stock of the order items from the listing
            if ($revert) {
                $item->increment('stock_quantity', $item->pivot->quantity);
            }
        }

        DB::table('cart_items')->insert($cart_items);

        if ($revert) {
            // Increment the coupon in use
            if ($order->coupon_id) {
                $coupon = Coupon::find($order->coupon_id);

                if ($coupon) {
                    $coupon->increment('quantity');
                }
            }

            $order->forceDelete();   // Delete the order
        }

        // Update cart
        $cart->quantity = $quantity;
        $cart->shipping_weight = $shipping_weight;
        $cart->total = $total;
        $cart->grand_total = $cart->calculate_grand_total();
        $cart->taxes = $cart->get_tax_amount();
        $cart->save();

        return $cart;
    }

    /**
     * Revert order to cart
     *
     * @param \App\Models\Order $Order
     *
     * @return \App\Models\Cart|null
     */
    private function getCartOwnerId($request, $cart = null)
    {
        if ($cart && $cart->customer_id) {
            return $cart->customer_id;
        }

        if (Auth::guard('customer')->check()) {
            return Auth::guard('customer')->user()->id;
        }

        if (Auth::guard('api')->check()) {
            return Auth::guard('api')->user()->id;
        }

        if ($request->api_token) {
            $customer = Customer::where('api_token', $request->api_token)->first();

            return $customer ? $customer->id : null;
        }

        return null;
    }

    /**
     * Check it the card already exist in the card cookie
     *
     * @param int $cartId
     * @return boolean
     */
    public function isCartInCookie($cartId)
    {
        $cartIds = cart_ids_from_cookie();

        return in_array($cartId, $cartIds);
    }

    /**
     * Push the new card id into the cart cookie
     *
     * @param array $cartIds
     * @return bool
     */
    public function pushCartIdsToCookie(array $cartIds)
    {
        $existingCartIds = cart_ids_from_cookie();

        // Merge existing cart IDs with new ones, ensuring no duplicates
        $mergedCartIds = array_unique(array_merge($existingCartIds, $cartIds));

        $cookieValue = implode(',', $mergedCartIds);

        return setcookie('cart_ids', $cookieValue, time() + (60 * 24 * 30), "/");
    }

    /**
     * Remove the given cart IDs from the cart cookie
     *
     * @param array $cartIds
     * @return void
     */
    public function removeCartIdsFromCookie(array $cartIds)
    {
        $existingCartIds = cart_ids_from_cookie();

        // Remove the cart IDs from existing cart IDs
        $mergedCartIds = array_values(array_diff($existingCartIds, $cartIds));

        $cookieValue = implode(',', $mergedCartIds);

        setcookie('cart_ids', $cookieValue, time() + (60 * 24 * 30), "/");
    }
}
