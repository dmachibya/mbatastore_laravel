<?php

namespace App\Http\Controllers\Admin;

use App\Models\Config;
use App\Models\PaymentMethod;

// use App\Common\Authorizable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    // use Authorizable;

    private $model_name;

    public function __construct()
    {
        parent::__construct();

        $this->model_name = trans('app.model.payment_method');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $config = $this->checkPermission($request, 'view');

        /**
         * When admin get paid but still give option to vendors on/off a active payment method
         */
        if (!vendor_get_paid_directly()) {
            return view('admin.config.payment-method.on_off');
        }

        return view('admin.config.payment-method.index');
    }

    /**
     * Activate a payment method.
     */
    public function activate(Request $request, $id)
    {
        $config = $this->checkPermission($request);
        $paymentMethod = PaymentMethod::findOrFail($id);

        $config->paymentMethods()->syncWithoutDetaching($id);

        if (!vendor_get_paid_directly()) {
            return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
        }

        return $this->getActivationRedirect($paymentMethod->code) ??
            back()->with('error', trans('messages.failed', ['model' => $this->model_name]));
    }

    /**
     * Get the appropriate activation redirect route for payment methods.
     */
    private function getActivationRedirect(string $paymentCode)
    {
        $routes = [
            'stripe' => 'admin.setting.stripe.connect',
            'authorizenet' => 'admin.setting.authorizenet.activate',
            'instamojo' => 'admin.setting.instamojo.activate',
            'iyzico' => 'admin.setting.iyzico.activate',
            'paypal' => 'admin.setting.paypal.activate',
            'payfast' => 'admin.setting.payfast.activate',
            'mercado-pago' => 'admin.setting.mercagoPago.activate',
            'paypal-express' => 'admin.setting.paypalExpress.activate',
            'paypal-marketplace' => 'admin.setting.paypalMarketplace.initiate',
            'paystack' => 'admin.setting.paystack.activate',
            'cybersource' => 'admin.setting.cybersource.activate',
            'razorpay' => 'admin.setting.razorpay.activate',
            'sslcommerz' => 'admin.setting.sslcommerz.activate',
            'flutterwave' => 'admin.setting.flutterwave.activate',
            'orangemoney' => 'admin.setting.orangemoney.activate',
            'mollie' => 'admin.setting.mollie.activate',
            'mpesa' => 'admin.setting.mpesa.activate',
            'bkash' => 'admin.setting.bkash.activate',
            'paytm' => 'admin.setting.paytm.activate',
            'twoCheckout' => 'admin.setting.twoCheckout.activate',
            'mtnMoney' => 'admin.setting.mtnMoney.activate',
            'upiPayment' => 'admin.setting.upiPayment.activate',

            // Manual Payment methods
            'wire' => 'admin.setting.manualPaymentMethod.activate',
            'pip' => 'admin.setting.manualPaymentMethod.activate',
            'wallet' => 'admin.setting.manualPaymentMethod.activate',
            'cod' => 'admin.setting.manualPaymentMethod.activate',
        ];

        return isset($routes[$paymentCode])
            ? redirect()->route($routes[$paymentCode], $paymentCode)
            : null;
    }

    public function deactivate(Request $request, $id)
    {
        if (config('app.demo') == true) {
            return back()->with('warning', trans('messages.demo_restriction'));
        }

        $config = $this->checkPermission($request);

        $paymentMethod = PaymentMethod::findOrFail($id);

        $config->paymentMethods()->detach($id);

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    public function activateManualPaymentMethod(Request $request, $code)
    {
        $config = $this->checkPermission($request);

        $paymentMethod = PaymentMethod::where('code', $code)->firstOrFail();

        $config->manualPaymentMethods()->syncWithoutDetaching($paymentMethod);

        $paymentMethod = $config->manualPaymentMethods->find($paymentMethod);

        return view('admin.config.payment-method.manual', compact('paymentMethod'));
    }

    public function deactivateManualPaymentMethod(Request $request, $code)
    {
        if (config('app.demo') == true) {
            return back()->with('warning', trans('messages.demo_restriction'));
        }

        $config = $this->checkPermission($request);

        $paymentMethod = PaymentMethod::where('code', $code)->firstOrFail();

        $config->manualPaymentMethods()->detach($paymentMethod->id);

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    public function updateManualPaymentMethod(Request $request, $code)
    {
        if (config('app.demo') == true) {
            return back()->with('warning', trans('messages.demo_restriction'));
        }

        $config = $this->checkPermission($request);

        $paymentMethod = PaymentMethod::where('code', $code)->firstOrFail();

        $data = [
            'additional_details' => $request->input('additional_details'),
            'payment_instructions' => $request->input('payment_instructions'),
        ];

        $config->manualPaymentMethods()->updateExistingPivot($paymentMethod->id, $data);

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    /**
     * Check permission
     *
     * @return $config
     */
    private function checkPermission(Request $request, $action = 'update')
    {
        $config = Config::findOrFail($request->user()->merchantId());

        $this->authorize($action, $config);

        return $config;
    }
}
