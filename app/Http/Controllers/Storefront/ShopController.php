<?php

namespace App\Http\Controllers\Storefront;

use Carbon\Carbon;
use App\Models\Shop;
use App\Models\Banner;
use App\Models\Slider;
use App\Models\Feedback;
use App\Models\Inventory;
use App\Helpers\ListHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\Validations\BrowseProductRequest;

class ShopController extends Controller
{
  /**
   * Open shop list page
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $shops = Shop::select('id', 'owner_id', 'slug', 'name', 'id_verified', 'phone_verified', 'address_verified', 'total_item_sold', 'total_sold_amount', 'created_at')
      ->with([
        'config',
        'logoImage',
        'owner:id,name,nice_name,email',
        'owner.avatarImage:path,imageable_id,imageable_type',
        // 'address:id,city,country_id,state_id,addressable_id,addressable_type',
        // 'address.state:id,name',
        'avgFeedback:rating,count,feedbackable_id,feedbackable_type',
      ])
      ->withCount([
        'inventories' => function ($q) {
          $q->available();
        },
      ])
      ->active()->paginate(24);

    return view('theme::shop_lists', compact('shops'));
  }

  /**
   * Open shop page
   *
   * @param  slug  $slug
   * @return \Illuminate\Http\Response
   */
  public function show($slug)
  {
    $shop = Shop::where('slug', $slug)->active()
      ->withCount([
        'inventories' => function ($q) {
          $q->available();
        },
      ])
      ->firstOrFail();

    // Check shop maintenance_mode
    if ($shop->isDown()) {
      return response()->view('theme::errors.503', [], 503);
    }

    $banners = Cache::rememberForever('banners' . $shop->id, function () use ($shop) {
      return Banner::with('featureImage:path,imageable_id,imageable_type')
        ->where('shop_id', $shop->id)
        ->orderBy('order', 'asc')->get()
        ->groupBy('group_id')->toArray();
    });

    // Deal of the day;
    $deal_of_the_day = get_deal_of_the_day($shop->id);

    // Get featured items
    $featured_items = get_featured_items($shop->id);

    // Top Selling Items
    $top_items = ListHelper::top_selling_shop_items($shop, 10);

    // Recently Added Items
    $recent = ListHelper::latest_shop_items($shop, 10);

    // Best deal under the amount
    $deals_under = Cache::rememberForever('deals_under' . $shop->id, function () use ($shop) {
      return ListHelper::best_find_under(best_finds_under($shop->id), 20, $shop->id);
    });

    $sliders = Cache::rememberForever('sliders' . $shop->id, function () use ($shop) {
      return Slider::orderBy('order', 'asc')
        ->where('shop_id', $shop->id)
        ->with([
          'featureImage:path,imageable_id,imageable_type',
          'mobileImage:path,imageable_id,imageable_type',
        ])
        ->get()->toArray();
    });

    return view('theme::shop', compact('shop', 'sliders', 'banners', 'featured_items', 'top_items', 'deal_of_the_day', 'deals_under', 'recent'));
  }

  /**
   * Show all products of the given shop
   *
   * @param  slug  $slug
   * @return \Illuminate\Http\Response
   */
  public function products(BrowseProductRequest $request, $slug)
  {
    $now = Carbon::now();
    $shop = Shop::where('slug', $slug)->active()->withCount([
      'inventories' => function ($q) {
        $q->available();
      },
    ])->firstOrFail();

    // Check shop maintenance_mode
    if ($shop->isDown()) {
      return response()->view('theme::errors.503', [], 503);
    }

    $all_products = Inventory::where('shop_id', $shop->id)
      ->groupBy('product_id')
      ->with([
        'avgFeedback:rating,count,feedbackable_id,feedbackable_type',
        'image:path,imageable_id,imageable_type',
      ])
      ->withCount(['orders' => function (\Illuminate\Database\Eloquent\Builder $q) use ($now) {
        $q->where('order_items.created_at', '>=', $now->subHours(config('system.popular.hot_item.period', 24)));
      }])->available();

    $forPriceRange = $all_products->get();
    $min = floor($forPriceRange->min('sale_price'));
    $max = ceil($forPriceRange->max('sale_price'));
    $priceRange = compact('min', 'max');

    // Filtering occurs after priceRange has been extracted.
    // $all_products = $all_products->filter($request->all())->inRandomOrder()->get();
    if ($request->sort_by) {
      $all_products = $all_products->filter($request->all())->get();
    } else {
      $all_products = $all_products->filter($request->all())->inRandomOrder()->get();
    }

    // $new = $all_products->where('condition', trans('app.new'))->count();
    // $used = $all_products->where('condition', trans('app.used'))->count();
    // $refurbished = $all_products->where('condition', trans('app.refurbished'))->count();
    // $productConditions = compact('new', 'used', 'refurbished');
    // $hasOffers = $all_products->where('offer_price', '>', 0)->where('offer_start', '<', $now)->where('offer_end', '>', $now)->count();
    // $hasFreeShipping = $all_products->where('free_shipping', 1)->count();
    // $newArrivals = $all_products->where('created_at', '>', $now->subDays(config('system.filter.new_arrival', 7)))->count();

    // Paginate the results
    $products = $all_products->paginate(16); // PLS 15 -> 16 products per page (4 rows by 4 products)

    return view('theme::shop', compact('shop', 'products', 'priceRange'));
    // return view('theme::shop', compact('shop', 'products', 'priceRange', 'hasOffers', 'hasFreeShipping', 'newArrivals', 'productConditions'));

    // $shop = Shop::where('slug', $slug)->active()->withCount([
    //   'inventories' => function ($q) {
    //     $q->available();
    //   },
    // ])->firstOrFail();

    // // Check shop maintenance_mode
    // if ($shop->isDown()) {
    //   return response()->view('theme::errors.503', [], 503);
    // }

    // $products = Inventory::where('shop_id', $shop->id)
    //   ->groupBy('product_id')
    //   ->filter($request->all())
    //   ->with([
    //     'avgFeedback:rating,count,feedbackable_id,feedbackable_type',
    //     'images:path,imageable_id,imageable_type',
    //   ])
    //   ->withCount(['orders' => function ($q) {
    //     $q->where('order_items.created_at', '>=', Carbon::now()->subHours(config('system.popular.hot_item.period', 24)));
    //   }])
    //   ->available()->inRandomOrder()->paginate(15);

    // return view('theme::shop', compact('shop', 'products'));
  }

  /**
   * Show all reviews of the given shop
   *
   * @param  slug  $slug
   * @return \Illuminate\Http\Response
   */
  public function reviews($slug)
  {
    $shop = Shop::where('slug', $slug)->active()->withCount([
      'inventories' => function ($q) {
        $q->available();
      },
    ])->firstOrFail();

    // Check shop maintenance_mode
    if ($shop->isDown()) {
      return response()->view('theme::errors.503', [], 503);
    }

    $reviews = Feedback::where([
      ['feedbackable_id', '=', $shop->id],
      ['feedbackable_type', '=', 'App\Models\Shop']
    ])->with('customer:id,nice_name,name')
      ->latest()->paginate(5);

    return view('theme::shop', compact('shop', 'reviews'));
  }
}
