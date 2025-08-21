<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Inventory;
use App\Http\Controllers\Controller;
use App\Http\Resources\ListingResource;
use App\Http\Requests\Validations\ProductSearchRequest;

class SearchController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(ProductSearchRequest $request)
  {
    $now = Carbon::now();

    $query = Inventory::search($request->get('search'))->where('active', 1)->paginate(0);

    $query = $query->where('available_from', '<=', $now);

    // Hide out-of-stock items when enabled
    if (config('system_settings.hide_out_of_stock_items')) {
      $query = $query->where('stock_quantity', '>', 0);
    }

    // Check expiry date when pharmacy plugin is enabled
    if (is_incevio_package_loaded('pharmacy')) {
      $query = $query->where('expiry_date', '>', $now);
    }

    $query->load(['shop:id,current_billing_plan,active']);

    // Keep results only from active shops
    $products = $query->filter(function ($product) {
      if (is_subscription_enabled()) {
        return ($product->shop->current_billing_plan !== null) &&
          ($product->shop->active == 1);
      }

      return $product->shop->active == 1;
    });

    if ($request->has('free_shipping')) {
      $products = $products->where('free_shipping', 1);
    }

    if ($request->has('new_arrivals')) {
      $products = $products->where('created_at', '>', $now->subDays(config('mobile_app.filter.new_arrival', 7)));
    }

    if ($request->has('has_offers')) {
      $products = $products->where('offer_price', '>', 0)
        ->where('offer_start', '<', $now)
        ->where('offer_end', '>', $now);
    }

    if ($request->has('condition')) {
      $products = $products->whereIn('condition', $request->input('condition'));
    }

    if ($request->has('price_min')) {
      $products = $products->where('sale_price', '>=', $request->input('price_min'));
    }

    if ($request->has('price_max')) {
      $products = $products->where('sale_price', '<=', $request->input('price_max'));
    }

    $products = $products->paginate(config('mobile_app.view_listing_per_page', 8));

    // Load avg rating
    $products = $products->load('avgFeedback:rating,count,feedbackable_id');

    return ListingResource::collection($products);
  }
}
