<?php

namespace App\Http\Controllers\Admin;

use App\Models\PdfTemplate;
use App\Models\Shop;
use App\Models\Config;
use Illuminate\Http\Request;
use App\Events\Shop\ShopIsLive;
use App\Events\Shop\ShopUpdated;
use App\Events\Shop\ConfigUpdated;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Events\Shop\DownForMaintainace;
use App\Http\Requests\Validations\UpdateConfigRequest;
use App\Http\Requests\Validations\MerchantVerifyRequest;
use App\Http\Requests\Validations\UpdateBasicConfigRequest;
use App\Http\Requests\Validations\ToggleMaintenanceModeRequest;

class ConfigController extends Controller
{
    // use Authorizable;

    private $model_name;

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->model_name = trans('app.model.config');
    }

    /**
     * Display the resource.
     *
     * @return \Illuminate\View\View
     */
    public function viewGeneralSetting()
    {
        // $files = \Illuminate\Support\Facades\Storage::disk('google')->allFiles();
        $shop = Shop::findOrFail(Auth::user()->merchantId());

        $shop_config = Config::find(Auth::user()->merchantId(), [
            'ac_holder_name',
            'ac_number',
            'ac_iban',
            'ac_swift_bic_code',
            'ac_routing_number',
            'ac_type',
            'ac_bank_address',
        ]);

        return view('admin.config.general', compact('shop', 'shop_config'));
    }

    /**
     * Display the resource.
     *
     * @return \Illuminate\View\View
     */
    public function view()
    {
        $config = Config::findOrFail(Auth::user()->merchantId());

        $this->authorize('view', $config); // Check permission

        $order_invoice_pdf_templates = PdfTemplate::active()->where('type', PdfTemplate::TYPE_ORDER_INVOICE)->get()->pluck('name', 'id');

        $shipping_label_pdf_templates = PdfTemplate::active()->where('type', PdfTemplate::TYPE_SHIPPING_LABEL)->get()->pluck('name', 'id');

        return view('admin.config.index', compact('config', 'order_invoice_pdf_templates', 'shipping_label_pdf_templates'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateBasicConfig(UpdateBasicConfigRequest $request, $id)
    {
        $config = Config::findOrFail($id);

        if (config('app.demo') == true && $config->shop_id <= config('system.demo.shops', 2)) {
            return back()->with('warning', trans('messages.demo_restriction'));
        }

        $this->authorize('update', $config); // Check permission

        $config->shop->update($request->all());

        event(new ShopUpdated($config->shop));

        if ($request->hasFile('logo') || ($request->input('delete_logo') == 1)) {
            $config->shop->deleteLogo();
        }

        if ($request->hasFile('logo')) {
            $config->shop->saveImage($request->file('logo'), 'logo');
        }

        if ($request->hasFile('cover_image') || ($request->input('delete_cover_image') == 1)) {
            $config->shop->deleteCoverImage();
        }

        if ($request->hasFile('cover_image')) {
            $config->shop->saveImage($request->file('cover_image'), 'cover');
        }

        if ($request->hasFile('stamp_image') || ($request->input('delete_stamp_image') == 1)) {
            $config->shop->deleteStampImage();
        }

        if ($request->hasFile('stamp_image')) {
            $config->shop->saveImage($request->file('stamp_image'), 'stamp');
        }

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    /**
     * Update configurations
     *
     * @param UpdateConfigRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function updateConfig(UpdateConfigRequest $request, $id)
    {
        if (config('app.demo') == true && $id <= config('system.demo.shops', 2)) {
            return response('error', 444);
        }

        $config = Config::findOrFail($id);

        $this->authorize('update', $config); // Check permission

        if ($config->update($request->all())) {
            event(new ConfigUpdated($config->shop, Auth::user()));

            clearShopConfigCache($id); // Clear cached values

            return response('success', 200);
        }

        return response('error', 405);
    }

    /**
     * Display the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function verify(MerchantVerifyRequest $request)
    {
        $config = Config::findOrFail(Auth::user()->merchantId());

        return view('admin.account.verify', compact('config'));
    }

    /**
     * Display the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function saveVerificationData(MerchantVerifyRequest $request)
    {
        $shop_id = Auth::user()->merchantId();

        $config = Config::findOrFail($shop_id);

        if ($request->hasFile('documents')) {
            $config->saveAttachments($request->file('documents'));
        }

        $config->update(['pending_verification' => 1]);

        clearShopConfigCache($shop_id); // Clear cached values

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    /**
     * Toggle Maintenance Mode of the given id, Its uses the ajax middleware
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $node
     * @return \Illuminate\Http\Response
     */
    public function toggleNotification(Request $request, $node)
    {
        $config = Config::findOrFail($request->user()->merchantId());

        if (config('app.demo') == true && $config->shop_id <= config('system.demo.shops', 2)) {
            return response('error', 444);
        }

        $this->authorize('update', $config); // Check permission

        $config->$node = !$config->$node;

        if ($config->save()) {
            event(new ConfigUpdated($config->shop, Auth::user()));

            clearShopConfigCache($config->shop_id); // Clear cached values

            return response('success', 200);
        }

        return response('error', 405);
    }

    /**
     * Toggle Maintenance Mode of the given id, Its uses the ajax middleware
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function toggleMaintenanceMode(ToggleMaintenanceModeRequest $request, $id)
    {
        if (config('app.demo') == true && $id <= config('system.demo.shops', 2)) {
            return response('error', 444);
        }

        $config = Config::findOrFail($id);

        $this->authorize('update', $config); // Check permission

        $config->maintenance_mode = !$config->maintenance_mode;

        if ($config->save()) {
            if ($config->maintenance_mode) {
                event(new DownForMaintainace($config->shop));
            } else {
                event(new ShopIsLive($config->shop));
            }

            clearShopConfigCache($config->shop_id); // Clear cached values

            return response('success', 200);
        }

        return response('error', 405);
    }

    /**
     * Populate the edit form for bank details
     *
     * @param int $shopId
     * @return \Illuminate\Http\Response
     */
    public function editBankInfo($shopId)
    {
        $bankInfo = Config::find($shopId, [
            'shop_id',
            'ac_holder_name',
            'ac_number',
            'ac_type',
            'ac_routing_number',
            'ac_swift_bic_code',
            'ac_iban',
            'ac_bank_address'
        ]);

        return view('admin.config._update_bank_info', compact('bankInfo'));
    }

    /**
     * Update the bank information
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateBankInfo(Request $request)
    {
        $shop_id = Auth::user()->merchantId();

        Config::find($shop_id)->update($request->all());

        clearShopConfigCache($shop_id); // Clear cached values

        return back()->with('success', trans('messages.created', ['model' => $this->model_name]));
    }
}
