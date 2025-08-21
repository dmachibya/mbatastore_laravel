<?php

namespace App\Http\Controllers\Admin;

use App\Models\Config;
use App\Models\ShippingMethod;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShippingMethodController extends Controller
{
    private $model_name;

    public function __construct()
    {
        parent::__construct();

        $this->model_name = trans('app.model.shipping_method');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('admin.config.shipping-method.index');
    }

    /**
     * Activate the specified shipping method.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $shippingMethodId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activate(Request $request, int $shippingMethodId)
    {
        $config = $this->checkPermission($request);

        $shippingMethod = ShippingMethod::findOrFail($shippingMethodId);

        $config->shippingMethods()->syncWithoutDetaching($shippingMethodId);

        if ($shippingMethod->code === 'shippo') {
            return redirect()->route('admin.setting.shippo.activate');
        }

        return back()->with('error', trans('messages.failed', ['model' => $this->model_name]));
    }


    /**
     * Deactivate the specified shipping method.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $shippingMethodId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deactivate(Request $request, int $shippingMethodId)
    {
        $config = $this->checkPermission($request);

        $config->shippingMethods()->detach($shippingMethodId);

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
