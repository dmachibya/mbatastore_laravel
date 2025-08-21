<?php

namespace App\Http\Controllers\Storefront;

use App\Models\Cart;
use App\Models\Shop;
use App\Models\State;
use App\Models\Country;
use App\Helpers\ListHelper;
use App\Common\ShoppingCart;
use App\Models\SystemConfig;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Validations\DirectCheckoutRequest;

class CheckoutController extends Controller
{
    use ShoppingCart;

    /**
     * Handles the Cart checkout process.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function checkout(Request $request, Cart $cart)
    {
        if (SystemConfig::CustomerNeedsApproval()) {
            $this->redirectIfCustomerNotApproved();
        }

        if ($cart->inventories[0]->isOutOfStock()) {
            return redirect()->route('cart.index')
                ->with('warning', trans('mobile.out_of_stock'));
        }

        if (!crosscheckCartOwnership($request, $cart)) {
            return redirect()->route('cart.index')
                ->with('warning', trans('theme.notify.please_login_to_checkout'));
        }

        $cart = crosscheckAndUpdateOldCartInfo($request, $cart);

        $shop = Shop::where('id', $cart->shop_id)->active()
            ->with('config')->first();

        // Abort if the shop is not exist or inactive
        abort_unless($shop, 406, trans('theme.notify.store_not_available'));

        if (vendor_get_paid_directly()) {
            $shop->load(['paymentMethods' => function ($q) {
                $q->active();
            }]);

            $paymentMethods = $shop->paymentMethods;
            if (!$paymentMethods) {
                return redirect()->route('cart.index')
                    ->with('warning', trans('theme.notify.seller_has_no_payment_method'));
            }
        } else {
            $paymentMethods = PaymentMethod::active()->get();
        }

        // Load coupon
        $cart->load('coupon:id,shop_id,name,code,value,min_order_amount,type');

        $customer = Auth::guard('customer')->check() ? Auth::guard('customer')->user() : null;

        $business_areas = Country::select('id', 'name', 'iso_code')
            ->orderBy('name', 'asc')->get();

        // State list of the country for ship_to dropdown
        $states = $cart->ship_to_state_id ? ListHelper::states($cart->ship_to_country_id) : [];

        $geoip = geoip(get_visitor_IP());

        $geoip_country = $business_areas->where('iso_code', $geoip->iso_code)->first();

        $geoip_state = State::select('id', 'name', 'iso_code', 'country_id')
            ->where('iso_code', $geoip->state)
            ->where('country_id', $geoip_country->id)
            ->first();

        $country_id = $cart->ship_to_country_id ?? $geoip_country->id;
        $state_id = $cart->ship_to_state_id ?? optional($geoip_state)->id;

        $shipping_zones[$cart->id] = get_shipping_zone_of($cart->shop_id, $country_id, $state_id);

        $shipping_options[$cart->id] = isset($shipping_zones[$cart->id]->id) ? getShippingRates($shipping_zones[$cart->id]->id, $cart) : 'NaN';

        if ($cart->shop->config->isPickupEnabled()) {
            $shop->load('warehouses.address');
        }

        if (is_incevio_package_loaded('dynamic-currency')) {     // converted cart currency
            $cart['total'] = get_dynamic_currency_value($cart->total);
            $cart['grand_total'] = get_dynamic_currency_value($cart->grand_total);
            $cart['discount'] = get_dynamic_currency_value($cart->discount);
            $cart['taxes'] = get_dynamic_currency_value($cart->tax);
        }

        // When packaging module available
        if (is_incevio_package_loaded('packaging')) {
            if (is_incevio_package_loaded('dynamic-currency')) {    // Prepare packaging info
                $cart->shop->packagings->map(function ($rate) {
                    $rate->cost = get_dynamic_currency_value($rate->cost);

                    return $rate;
                });
            }

            $shop->load(['packagings' => function ($query) {
                $query->active();
            }]);

            $platformDefaultPackaging = getPlatformDefaultPackaging();

            return view('theme::checkout', compact('cart', 'customer', 'shop', 'business_areas', 'shipping_zones', 'shipping_options', 'states', 'paymentMethods', 'platformDefaultPackaging'));
        }

        return view('theme::checkout', compact('cart', 'customer', 'shop', 'business_areas', 'shipping_zones', 'shipping_options', 'states', 'paymentMethods'));
    }


    /**
     * Direct checkout with the item/cart
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $slug
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function directCheckout(DirectCheckoutRequest $request, $slug)
    {
        $cart = $this->addToCart($request, $slug);

        $cartData = $cart->getdata();

        if (200 == $cart->status() && is_object($cartData) && property_exists($cartData, '0')) {
            $firstCartItem = $cartData->{'0'};
            $cartId = $firstCartItem->id;

            return redirect()->route('cart.index', $cartId);
        }

        if (444 == $cart->status()) {
            return redirect()->route('cart.index', $cartData->cart_id);
        }

        return redirect()->back()->with('warning', trans('theme.notify.failed'));
    }

    /**
     * Redirects the customer to the cart page if they are not approved yet.
     *
     * @return \Illuminate\Http\RedirectResponse|null
     */
    private function redirectIfCustomerNotApproved()
    {
        $user = Auth::guard('customer')->user();

        if ($user instanceof Customer ? !$user->isApproved() : false) {
            return redirect()->route('cart.index')->with('warning', trans('help.account_needs_approval'));
        }
    }
}
