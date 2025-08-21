<?php

namespace App\Services\Payments;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Rest\ApiContext;
use Twilio\Rest\Api;

class PaypalPaymentService extends PaymentService
{
    private $client_id;
    private $client_secret;
    private $api_context;
    private $mode;

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->client_id = config('paypal_payment.account.client_id');
        $this->client_secret = config('paypal_payment.account.client_secret');

        $this->api_context = new ApiContext(new OAuthTokenCredential($this->client_id, $this->client_secret));
        $this->api_context->setConfig(config('paypal_payment.settings'));
    }


    public function setConfig()
    {
        // Get the vendor configs
        if ($this->receiver == 'merchant') {
            $vendorConfig = $this->order->shop->config->paypal;

            $this->client_id = $vendorConfig->client_id;
            $this->client_secret = $vendorConfig->client_secret;

            $this->mode = $vendorConfig->sandbox == 1 ? 'sandbox' : 'live';

            $this->api_context->setConfig(['mode' => $this->mode]);
        }

        if ($this->order) {
            $items = [];
            $taxes = 0;
            $packaging = 0;
            $discount = 0;
            $shipping = 0;
            $total = 0;

            if (is_array($this->order)) {
                foreach ($this->order as $tOrder) {
                    $taxes += $tOrder->taxes;
                    $packaging += $tOrder->packaging;
                    $discount += $tOrder->discount;
                    $shipping += $tOrder->get_shipping_cost();

                    foreach ($tOrder->inventories as $item) {
                        $total += (format_price_for_paypal($item->pivot->unit_price) * $item->pivot->quantity);

                        $items[] = $this->setPayPalItem(
                            $item->title,
                            $item->pivot->unit_price,
                            $item->pivot->quantity,
                            $tOrder->taxrate,
                            $item->pivot->item_description
                        );
                    }
                }
            } else {
                $taxes = $this->order->taxes;
                $packaging = $this->order->packaging;
                $discount = $this->order->discount;
                $shipping = $this->order->get_shipping_cost();

                foreach ($this->order->inventories as $item) {
                    $total += (format_price_for_paypal($item->pivot->unit_price) * $item->pivot->quantity);

                    $items[] = $this->setPayPalItem(
                        $item->title,
                        $item->pivot->unit_price,
                        $item->pivot->quantity,
                        $this->order->taxrate,
                        $item->pivot->item_description
                    );
                }
            }

            $paymentMethod = is_array($this->order) ? $this->order[0]->paymentMethod : $this->order->paymentMethod;

            $returnUrl = route('payment.success', ['gateway' => $paymentMethod->code, 'order' => $this->getOrderId()]);
            $cancelUrl = route('payment.failed', ['order' => $this->getOrderId()]);

            $details = new Details();
            $details->setShipping($shipping)
                ->setTax($taxes)
                ->setGiftWrap($packaging)
                ->setShippingDiscount($discount)
                ->setSubtotal(format_price_for_paypal($total)); //total of items prices
        } else {
            $items[] = $this->setPayPalItem($this->description, $this->amount, 1, 0, $this->description);

            $returnUrl = route('wallet.deposit.paypal.success');
            $cancelUrl = route('wallet.deposit.failed');

            $details = new Details();
            $details->setShipping(0)->setTax(0)
                ->setSubtotal($this->amount); //total of items prices
        }

        // Set Items
        $itemList = new ItemList();
        $itemList->setItems($items);

        // Set Redirect Urls
        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl($returnUrl)
            ->setCancelUrl($cancelUrl);

        $this->setDescription($details);

        $this->redirectUrls = $redirectUrls;

        $payer = new Payer();
        $this->payee = $payer->setPaymentMethod('paypal');

        //Payment Amount
        $amount = new Amount();
        $amount->setCurrency(get_currency_code())
            ->setTotal($this->amount)
            ->setDetails($this->description);

        // ### Transaction
        // A transaction defines the contract of a payment - what is the payment for and who
        // is fulfilling it. Transaction is created with a `Payee` and `Amount` types
        $transaction = new Transaction();
        $this->transaction = $transaction->setAmount($amount)
            ->setItemList($itemList);
        // ->setInvoiceNumber($this->order->order_number)
        // ->setDescription($this->description);

        return $this;
    }

    public function charge()
    {
        $payment = new Payment();
        $payment->setIntent('sale')
            ->setPayer($this->payee)
            ->setTransactions([$this->transaction])
            ->setRedirectUrls($this->redirectUrls);

        $payment->create($this->api_context);

        return redirect()->to($payment->getApprovalLink());
    }

    public function paymentExecution($paymentId, $payerID)
    {
        $payment = Payment::get($paymentId, $this->api_context);

        // Execute the payment;
        try {

            $paymentExecution = new PaymentExecution();
            $paymentExecution->setPayerId($payerID);
            $payment->execute($paymentExecution, $this->api_context);

            $this->status = self::STATUS_PAID;
            $this->response = $payment;
        } catch (PayPalConnectionException $ex) {
            $this->status = self::STATUS_ERROR;

            // return $ex;
        }

        return $this;
    }

    public function setAmount($amount)
    {
        $this->amount = format_price_for_paypal($amount);

        return $this;
    }

    private function setPayPalItem($title, $unit_price, $quantity = 1, $taxrate = 0, $description = '')
    {
        $tempItem = new Item();

        return $tempItem->setName($title)
            ->setDescription($description)
            ->setQuantity($quantity)
            ->setCurrency(get_currency_code())
            ->setTax($taxrate > 0 ? format_price_for_paypal($taxrate) : 0)
            ->setPrice(format_price_for_paypal($unit_price));
    }

    /**
     * Verify Paid Payment
     */
    public function verifyPaidPayment()
    {
        $payment_meta = json_decode($this->request->input('payment_meta'));

        try {
            if ($payment_meta->paymentId && $payment_meta->payerID) {
                // Verify the payment;
                $this->paymentExecution($payment_meta->paymentId, $payment_meta->payerID);

                $this->status = self::STATUS_PAID;
            }
        } catch (\Exception $e) {
            $this->status = self::STATUS_ERROR;

            Log::error($e);
        }

        return $this;
    }

}
