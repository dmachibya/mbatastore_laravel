<?php

namespace App\Http\Controllers\Storefront;

use Exception;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Inventory;
use App\Models\Attachment;
use App\Common\ShoppingCart;
use Illuminate\Http\Request;
use App\Events\Order\OrderCreated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Services\Payments\PaymentService;
use App\Exceptions\PaymentFailedException;
use App\Services\Payments\PaypalPaymentService;
use App\Http\Requests\Validations\OrderDetailRequest;
use App\Http\Requests\Validations\CheckoutCartRequest;
use App\Contracts\PaymentServiceContract as PaymentGateway;
use App\Http\Requests\Validations\ConfirmGoodsReceivedRequest;

class OrderController extends Controller
{
    use ShoppingCart;

    /**
     * Checkout the cart and process the payment.
     *
     * @param CheckoutCartRequest $request The request object containing the cart data.
     * @param Cart $cart The cart object.
     * @param PaymentGateway $payment The payment gateway object.
     * @throws PaymentFailedException If the payment fails.
     * @return \Illuminate\Http\RedirectResponse The response to redirect to the payment gateway or the order complete page.
     */
    public function create(CheckoutCartRequest $request, Cart $cart, PaymentGateway $payment)
    {
        $cart = crosscheckAndUpdateOldCartInfo($request, $cart);

        DB::beginTransaction();

        try {
            $order = $this->saveOrderFromCart($request, $cart);     // Create the order

            $order->fulfilment_type = $request->fulfilment_type;      // Assign the fulfillment type

            if (is_incevio_package_loaded('dynamic-currency')) {    // Added Converted Currency Details
                $order['exchange_rate'] = get_dynamic_currency_attr('exchange_rate');
                $order['currency_id'] = get_dynamic_currency_attr('id');
            }

            $receiver = vendor_get_paid_directly() ? PaymentService::RECEIVER_MERCHANT : PaymentService::RECEIVER_PLATFORM;

            $response = $payment->setReceiver($receiver)
                ->setOrderInfo($order)
                ->setAmount($order->grand_total)
                ->setDescription(trans('app.purchase_from', [
                    'marketplace' => get_platform_title()
                ]))
                ->setConfig()
                ->charge();

            // Check if the response needs to redirect to gateways
            if ($response instanceof RedirectResponse) {
                DB::commit();
                return $response;
            }

            switch ($response->status) {
                case PaymentService::STATUS_PAID:
                    $order->markAsPaid();     // Order has been paid
                    break;

                case PaymentService::STATUS_PENDING:
                    if ($order->paymentMethod->code == 'cod') {
                        $order->order_status_id = Order::STATUS_CONFIRMED;
                        $order->payment_status = Order::PAYMENT_STATUS_UNPAID;
                    } else {
                        $order->order_status_id = Order::STATUS_WAITING_FOR_PAYMENT;
                        $order->payment_status = Order::PAYMENT_STATUS_PENDING;
                    }
                    break;

                case PaymentService::STATUS_ERROR:
                    $order->payment_status = Order::PAYMENT_STATUS_PENDING;
                    $order->order_status_id = Order::STATUS_PAYMENT_ERROR;
                default:
                    throw new PaymentFailedException(trans('theme.notify.payment_failed'));
            }

            // Save the order
            $order->save();
        } catch (Exception $e) {
            DB::rollback(); // Rollback the transaction and log the error

            Log::error($request->payment_method . ' payment failed:: ' . $e->getMessage());
            Log::error($e);

            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }

        // Everything is fine. Now commit the transaction
        DB::commit();

        // Trigger the Event
        event(new OrderCreated($order));

        $cart_item_count = cart_item_count();               // Update the cart count

        // $order->load('inventories.product');

        return view('theme::order_complete', compact('order', 'cart_item_count'))
            ->with('success', trans('theme.notify.order_placed'));
    }
    /**
     * Return from payment gateways with payment success
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Order $order
     * @param string $gateway Payment gateway code
     *
     * @return \Illuminate\Http\Response
     */
    public function paymentGatewaySuccessResponse(Request $request, $gateway, $order)
    {
        // Verify Payment Gateway Calls
        if (!$this->verifyPaymentGatewayCalls($request, $gateway)) {
            return redirect()->route('payment.failed', $order);
        }

        if ($gateway == 'paypal') {
            try {
                $service = new PaypalPaymentService($request);

                $response = $service->paymentExecution($request->get('paymentId'), $request->get('PayerID'));

                // If the payment failed
                if ($response->status != PaymentService::STATUS_PAID) {
                    return redirect()->route('payment.failed', $order);
                }
            } catch (Exception $e) {
                Log::error('Paypal payment failed on execution step:: ');
                Log::error($e->getMessage());
            }
        }
        // Order has been paid

        // OneCheckout plugin
        $orders = explode('-', $order);
        $order = count($orders) > 1 ? $orders : $order;
        if (is_array($order)) {
            foreach ($order as $id) {
                $temp = Order::findOrFail($id);

                $temp->markAsPaid();
            }

            $order = $temp;
        } else {
            // Single order
            if (!$order instanceof Order) {
                $order = Order::findOrFail($order);
            }

            $order->markAsPaid();
        }

        // Trigger the Event
        event(new OrderCreated($order));

        return view('theme::order_complete', compact('order'))
            ->with('success', trans('theme.notify.order_placed'));
    }

    /**
     * Payment failed or cancelled
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Order $order
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function paymentFailed(Request $request, $order)
    {
        if (!is_array($order)) {
            $orders = explode('-', $order);
            $order = count($orders) > 1 ? $orders : $order;
        }

        if (is_array($order)) {
            $cart = [];
            foreach ($order as $temp) {
                $cart[] = $this->moveAllItemsToCartAgain($temp, true);
            }
        } else {
            $cart = $this->moveAllItemsToCartAgain($order, true);
        }

        // Set failed message
        $msg = trans('theme.notify.payment_failed');

        $errors = $request->session()->get('errors');
        if (isset($errors) && count($errors) > 0) {
            $msg = $errors->all()[0];
        }

        if (is_array($cart)) {
            return redirect()->route('cart.index')->with('error', $msg);
        }

        return redirect()->route('cart.checkout', $cart)->with('error', $msg);
    }

    /**
     * Display order detail page.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Order $order
     *
     * @return \Illuminate\View\View
     */
    public function detail(OrderDetailRequest $request, Order $order)
    {
        $order->load(['inventories.image', 'conversation.replies.attachments']);

        return view('theme::order_detail', compact('order'));
    }

    /**
     * Buyer confirmed goods received
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Order $order
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function goods_received(ConfirmGoodsReceivedRequest $request, Order $order)
    {
        $order->mark_as_goods_received();

        return redirect()->route('order.feedback', $order)
            ->with('success', trans('theme.notify.order_updated'));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\Response
     */
    public function invoice(Order $order)
    {
        // $this->authorize('view', $order); // Check permission

        return $order->invoice(); // Download the invoice
    }

    public function downloadShippingLabel(Order $order)
    {
        return $order->shippingLabelPdf();
    }

    /**
     * Track order shipping.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Order $order
     *
     * @return \Illuminate\View\View
     */
    public function track(Request $request, Order $order)
    {
        return view('theme::order_tracking', compact('order'));
    }

    /**
     * Order again by moving all items into th cart
     */
    public function again(Request $request, Order $order)
    {
        $cart = $this->moveAllItemsToCartAgain($order);

        // If any waring returns from cart, normally out of stock items
        if (Session::has('warning')) {
            return redirect()->route('cart.index');
        }

        return redirect()->route('cart.checkout', $cart)
            ->with('success', trans('theme.notify.cart_updated'));
    }

    /**
     * Verify Payment Gateway Calls
     *
     * @param \Illuminate\Http\Request $request
     * @param string $gateway
     *
     * @return bool
     */
    private function verifyPaymentGatewayCalls(Request $request, $gateway)
    {
        switch ($gateway) {
            case 'paypal':
                return $request->has('token') && $request->has('paymentId') && $request->has('PayerID');

            case 'mollie':
                $mollie = new \Incevio\Package\Mollie\Services\MolliePaymentService($request);
                $mollie->setConfig();
                $mollie->verifyPaidPayment();

                return $mollie->status == PaymentService::STATUS_PAID;

            case 'bkash':
                if ($request->status != 'success') {
                    return false;
                }

                $bkash = new \Incevio\Package\Bkash\Services\BkashPaymentService($request);
                $bkash->setConfig();
                $bkash->verifyPaidPayment();

                return $bkash->status == PaymentService::STATUS_PAID;

            case 'stripeWeb':
                $stripeWeb = new \App\Services\Payments\StripeWebPaymentService($request);
                $stripeWeb->setConfig();
                $stripeWeb->verifyPaidPayment();

                return $stripeWeb->status == PaymentService::STATUS_PAID;

            case 'paytm':
                $paytm = new \Incevio\Package\Paytm\Services\PaytmPaymentService($request);
                $paytm->setConfig();
                $paytm->verifyPaidPayment();

                return $paytm->status == PaymentService::STATUS_PAID;
        }

        return false;
    }

    private function logErrors($error, $feedback)
    {
        Log::error($error);

        // Set error messages:
        // $error = new \Illuminate\Support\MessageBag();
        // $error->add('errors', $feedback);

        return $error;
    }

    /**
     * download attachment file
     *
     * @param Request $request
     * @param Attachment $attachment
     *
     * @return file
     */
    public function download(Request $request, Attachment $attachment, $order, Inventory $inventory)
    {
        // Check the existence of the file
        if (!Storage::exists($attachment->path)) {
            return back()->with('error', trans('messages.file_not_exist'));
        }

        $currentValue = DB::table('order_items')
            ->where('order_id', $order)
            ->where('inventory_id', $inventory->id)
            ->value('download');

        if ($currentValue >= $inventory->download_limit) {
            $msg = trans('messages.reached_download_maximum_limit');

            if (url()->previous()) {
                return back()->with('error', $msg);
            }

            return redirect('/')->with('error', $msg);
        }

        // Increment the download count
        DB::table('order_items')
            ->where('order_id', $order)
            ->where('inventory_id', $inventory->id)
            ->increment('download');

        return Storage::download($attachment->path, $attachment->name);
    }
}
