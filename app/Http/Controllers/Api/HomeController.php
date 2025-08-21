<?php

namespace App\Http\Controllers\Api;

use App\Models\Page;
use App\Models\Shop;
use App\Models\State;
use App\Models\Banner;
use App\Models\Slider;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Manufacturer;
use App\Models\PaymentMethod;
use App\Common\InventorySearch;
use App\Http\Controllers\Controller;
use App\Http\Resources\PageResource;
use App\Http\Resources\ShopResource;
use App\Http\Resources\StateResource;
use App\Http\Resources\BannerResource;
use App\Http\Resources\SliderResource;
use App\Http\Resources\CountryResource;
use App\Http\Resources\CurrencyResource;
use App\Http\Resources\ShopLightResource;
use App\Http\Resources\ManufacturerResource;
use App\Http\Resources\SystemConfigResource;
use App\Http\Resources\PaymentMethodResource;
use App\Http\Resources\ShippingOptionResource;
use App\Http\Resources\ManufacturerLightResource;
use App\Http\Resources\WarehouseResource;
use App\Http\Requests\Validations\ShippingOptionRequest;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    use InventorySearch;

    protected function setUp(): void
    {
        parent::setUp();

        Request::shouldReceive('ip')->andReturn('127.0.0.1');
    }

    /**
     * Get system's default configs.
     *
     * @return SystemConfigResource resource containing systems configs
     */
    public function system_configs()
    {
        $config = (object) config('system_settings');

        return  new SystemConfigResource($config);
    }

    /**
     * Get listing of the resource.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection A collection of SliderResources
     */
    public function sliders(Request $request)
    {
        $shop_id = null;

        if ($request->has('shop_id')) {
            $shop_id = $request->get('shop_id');
        }

        $sliders = Slider::whereHas('mobileImage')
          ->with('mobileImage')
          ->where('shop_id', $shop_id)
          ->orderBy('order', 'asc')
          ->get();

        return SliderResource::collection($sliders);
    }

    
    /**
     * Get listing of the Banner resource.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection A collection of BannerResources
     */
    public function banners(Request $request)
    {
        $shop_id = null;

        if ($request->has('shop_id')) {
            $shop_id = $request->get('shop_id');
        }

        $banners = Banner::with(['featureImage'])
          ->where('shop_id', $shop_id)
          ->orderBy('order', 'asc')
          ->get();

        return BannerResource::collection($banners);
    }

    /**
     * Display Basic Details of all shops.
     * 
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection The collection of ShopLightResource
     */
    public function allShops()
    {
        $shops = Shop::with([
            'logoImage:path,imageable_id,imageable_type',
            'avgFeedback:rating,count,feedbackable_id,feedbackable_type'
        ])
            ->withCount([
                'inventories' => function ($q) {
                    $q->available();
                },
            ])
            ->active()->get();

        return ShopLightResource::collection($shops);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $slug The slug of the shop.
     * @return ShopResource Contains shop details
     */
    public function shop($slug)
    {
        $shop = Shop::where('slug', $slug)->active()
            ->with([
                'latestFeedbacks' => function ($q) {
                    $q->with('customer:id,nice_name,name')->take(3);
                },
            ])
            ->withCount([
                'inventories' => function ($q) {
                    $q->available();
                },
            ])
            ->firstOrFail();

        // Check shop maintenance_mode
        if ($shop->isDown()) {
            return response()->json(['message' => trans('app.marketplace_down')], 404);
        }

        return new ShopResource($shop);
    }

    /**
     * Display warehouse resource for specified shop by slug.
     *
     * @param string $slug The slug of the shop.
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection The collection of warehouse resources
     */
    public function showAllWarehousesOfShop(string $slug)
    {
        $shop = Shop::where('slug', $slug)->active()->firstOrFail();
        
        if(!$shop->config->isPickupEnabled()){
            return response()->json(['message'=>trans('app.pickup_not_available')], 404);
        }

        $warehouses = $shop->warehouses()->active()->get();

        return WarehouseResource::collection($warehouses);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allBrands()
    {
        $brands = Manufacturer::select('id', 'name', 'slug', 'description', 'country_id')
            // ->with('country:id,name')
            ->with('logoImage:path,imageable_id,imageable_type')
            ->active()->get();

        return ManufacturerLightResource::collection($brands);
    }

    /**
     * Featured Brands
     *
     * @return void
     */
    public function featuredBrands()
    {
        $brands = get_featured_brands();

        return ManufacturerLightResource::collection($brands);
    }

    /**
     * Display the details of the brand with the given slug.
     *
     * @param  string  $slug The slug of the brand.
     * @return ManufacturerResource Contains brand/Manufacturer details
     */
    public function brand($slug)
    {
        $brand = Manufacturer::where('slug', $slug)->firstOrFail();

        return new ManufacturerResource($brand);
    }

    /**
     * Return available shipping options for the specified shop.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $shop
     * @return \Illuminate\Http\Response
     */
    public function shipping(ShippingOptionRequest $request, Shop $shop)
    {
        $shippingOptions = $request->cart ? getShippingRates($request->zone, $request->cart) : getShippingRates($request->zone);

        return ShippingOptionResource::collection($shippingOptions);
    }

    /**
     * Return available payment options options for the specified shop.
     *
     * @param  string  $shop
     * @return \Illuminate\Http\Response
     */
    public function paymentOptions($shop)
    {
        // Get the shop
        $shop = Shop::where('slug', $shop)->active()->firstOrFail();

        // Get all active payment methods
        $activePaymentMethods = PaymentMethod::active()->get();
        $activePaymentCodes = $activePaymentMethods->pluck('code')->toArray();

        $activePaymentMethods = $shop->paymentMethods;

        // $shop_config = null;
        // if (vendor_get_paid_directly()) {
        //     $activePaymentMethods = $shop->paymentMethods;
        //     $shop_config = $shop;
        // }

        $results = collect([]);
        foreach ($activePaymentMethods as $payment) {
            if (
                !in_array($payment->code, $activePaymentCodes) ||
                !get_payment_config_info($payment->code, $shop)
            ) {
                continue;
            }

            $results->push($payment);
        }

        return PaymentMethodResource::collection($results);
    }

    /**
     * Get active currencies.
     *
     * @return \Illuminate\Http\Response
     */
    public function currencies()
    {
        $currencies = Currency::active()->orderBy('priority', 'asc')->get();

        return CurrencyResource::collection($currencies);
    }

    /**
     * Get country list resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function countries()
    {
        $countries = Country::select('id', 'name', 'iso_code')->get();

        return CountryResource::collection($countries);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $country
     * @return \Illuminate\Http\Response
     */
    public function states($country)
    {
        $states = State::select('id', 'name', 'iso_code')
            ->where('country_id', $country)
            ->get();

        return StateResource::collection($states);
    }

    /**
     * Returns the details of the page with the given slug.
     *
     * @param  string  $slug The slug of the page.
     * @return PageResource Contains page details
     */
    public function page($slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();

        return new PageResource($page);
    }
}
