<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConfigPaypal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ConfigPaypalController extends Controller
{
    private $model_name;

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->model_name = trans('app.model.payment_method');
    }

    /**
     * Activate the Paypal checkout gateway
     *
     * @return \Illuminate\Http\Response
     */
    public function activate()
    {
        if (config('app.demo') == true) {

            return view('demo_modal');
        }
        $paypal = ConfigPaypal::firstOrCreate(['shop_id' => Auth::user()->merchantId()]);

        return view('admin.config.payment-method.paypal', compact('paypal'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $paypal = ConfigPaypal::firstOrCreate(['shop_id' => Auth::user()->merchantId()]);

        $paypal->update($request->all());

        return back()->with('success', trans('messages.created', ['model' => $this->model_name]));
    }


    /**
     * Deactivate the Paypal Express checkout gateway
     *
     * @return \Illuminate\Http\Response
     */
    public function deactivate()
    {
        $paypal = ConfigPaypal::firstOrCreate(['shop_id' => Auth::user()->merchantId()]);

        $paypal->delete();

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

}
