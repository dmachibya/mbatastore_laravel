<?php

namespace App\Http\Controllers\Storefront;

use Carbon\Carbon;
use App\Models\Page;
use App\Models\Banner;
use App\Models\Slider;
use App\Models\Country;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\Inventory;
use App\Helpers\ListHelper;
use App\Models\Manufacturer;
use App\Models\CategoryGroup;
use App\Common\InventorySearch;
use App\Models\CategorySubGroup;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\Validations\BrowseProductRequest;

class HomeController extends Controller
{
    // To search in inventory
    use InventorySearch;

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $sliders = Cache::rememberForever('sliders', function () {
            return Slider::orderBy('order', 'asc')
                ->with([
                    'featureImage:path,imageable_id,imageable_type',
                    'mobileImage:path,imageable_id,imageable_type',
                ])
                ->where('shop_id', null)
                ->get()->toArray();
        });

        $banners = Cache::rememberForever('banners', function () {
            return Banner::with('featureImage:path,imageable_id,imageable_type')
                ->whereNull('shop_id')
                ->orderBy('order', 'asc')->get()
                ->groupBy('group_id')->toArray();
        });

        //Trending Category Load With Images
        $trending_categories = get_trending_categories_with_items();

        //Featured Category Load With Images
        $featured_category = get_featured_category();

        //Featured Brands
        $featured_brands = get_featured_brands();

        //Featured Vendors
        $featured_vendors = get_featured_vendors();

        // Deal of the day;
        $deal_of_the_day = get_deal_of_the_day();

        // Get featured items
        $featured_items = get_featured_items();

        // Recently Added Items
        $recent = ListHelper::latest_available_items(10);

        // Best deal under the amount:
        $deals_under = Cache::rememberForever('deals_under', function () {
            return ListHelper::best_find_under(best_finds_under());
        });

        // Flash deals
        $flashdeals = get_flash_deals();

        // For legacy theme support. Will be removed in future
        if (active_theme() == 'legacy' || active_theme() == 'martfury') {
            // Trending items
            $trending = Cache::remember('popular_items', config('auction.cache_auction_items'), function () {
                return ListHelper::popular_items(config('system.popular.period.daily', 1), config('system.popular.take.trending', 12));
            });

            View::share('trending', $trending);
        }

        return view('theme::index', compact(
            'banners',
            'sliders',
            'recent',
            'trending_categories',
            'featured_items',
            'deal_of_the_day',
            'deals_under',
            'featured_category',
            'featured_brands',
            'featured_vendors',
            'flashdeals',
        ));
    }

    /**
     * Browse category based products
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function browseCategory(BrowseProductRequest $request, $slug, $sortby = null)
    {
        $category = Category::where('slug', $slug)
            ->with([
                'subGroup' => function ($q) {
                    $q->select(['id', 'slug', 'name', 'category_group_id'])->active();
                },
                'subGroup.group' => function ($q) {
                    $q->select(['id', 'slug', 'name'])->active();
                },
                'attrsList' => function ($q) {
                    $q->with('attributeValues');
                }
            ])
            ->active()->firstOrFail();

        $all_products = $category->listings()->available()->with([
            'avgFeedback:rating,count,feedbackable_id,feedbackable_type',
            'shop:id,slug,name,id_verified,phone_verified,address_verified',
            'image:path,imageable_id,imageable_type',
        ]);

        $filter = $all_products->get();
        $min = floor($filter->min('sale_price'));
        $max = ceil($filter->max('sale_price'));
        $priceRange = compact('min', 'max');

        // Filtering occurs after priceRange has been extracted.
        $products = $all_products->filter($request->all())->get();

        $now = Carbon::now();

        // Paginate the results
        $products = $products->paginate(config('system.view_listing_per_page', 16))
            ->appends($request->except('page'));

        return view('theme::category', compact('category', 'products', 'priceRange'));
    }

    /**
     * Browse listings by category sub group
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function browseCategorySubGrp(BrowseProductRequest $request, $slug, $sortby = null)
    {
        $now = Carbon::now();
        $min = null;
        $max = null;
        $new = null;
        $used = null;
        $refurbished = null;

        $categorySubGroup = CategorySubGroup::where('slug', $slug)
            ->with([
                'categories' => function (\Illuminate\Database\Eloquent\Relations\HasMany $q) {
                    $q->select(['id', 'slug', 'category_sub_group_id', 'name'])->whereHas('listings')->active();
                },
                'categories.listings' => function (\Illuminate\Database\Eloquent\Relations\BelongsToMany $d) use ($now, $request, &$min, &$max, &$new, &$used, &$refurbished) {
                    /** @var \App\Models\Inventory $d */
                    $all_results = $d->available()->get();
                    $min = floor($all_results->min('sale_price'));
                    $max = ceil($all_results->max('sale_price'));

                    $results2 = $d->available()->filter($request->all())->withCount([
                        'orders' => function (\Illuminate\Database\Eloquent\Builder $query) use ($now) {
                            $query->where('order_items.created_at', '>=', $now->subHours(config('system.popular.hot_item.period', 24)));
                        },
                    ])
                        ->with([
                            'avgFeedback:rating,count,feedbackable_id,feedbackable_type',
                            'shop:id,slug,name,id_verified,phone_verified,address_verified',
                            'image:path,imageable_id,imageable_type',
                        ])->get();
                },
            ])
            ->active()->firstOrFail();

        /** @var \Illuminate\Database\Eloquent\Builder $all_products **/
        $all_products = prepareFilteredListingsNew($request, $categorySubGroup->categories);

        $priceRange = compact('min', 'max');

        // Paginate the results
        $products = $all_products->paginate(config('system.view_listing_per_page', 16))
            ->appends($request->except('page'));

        return view('theme::category_sub_group', compact('categorySubGroup', 'products', 'priceRange'));
    }

    /**
     * Browse listings by category group
     *
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function browseCategoryGroup(BrowseProductRequest $request, $slug, $sortby = null)
    {
        $now = Carbon::now();
        $min = null;
        $max = null;
        $new = null;
        $used = null;
        $refurbished = null;

        $categoryGroup = CategoryGroup::where('slug', $slug)->with([
            'categories' => function (\Illuminate\Database\Eloquent\Relations\HasManyThrough $q) {
                $q->select(['categories.id', 'categories.slug', 'categories.category_sub_group_id', 'categories.name'])
                    ->where('categories.active', 1)->whereHas('listings')->withCount('listings');
            },
            'categories.listings' => function (\Illuminate\Database\Eloquent\Relations\BelongsToMany $d) use ($now, $request, &$min, &$max, &$new, &$used, &$refurbished) {
                /** @var \App\Models\Inventory $d */
                $all_results = $d->available()->get();
                $min = floor($all_results->min('sale_price'));
                $max = ceil($all_results->max('sale_price'));

                $results2 = $d->available()->filter($request->all())->withCount([
                    'orders' => function ($query) use ($now) {
                        $query->where('order_items.created_at', '>=', $now->subHours(config('system.popular.hot_item.period', 24)));
                    },
                ])->with([
                    'avgFeedback:rating,count,feedbackable_id,feedbackable_type',
                    'shop:id,slug,name,id_verified,phone_verified,address_verified',
                    'image:path,imageable_id,imageable_type',
                ])->get();

                $new = $results2->where('condition', trans('app.new'))->count();
                $used = $results2->where('condition', trans('app.used'))->count();
                $refurbished = $results2->where('condition', trans('app.refurbished'))->count();
            },
        ])->active()->firstOrFail();

        /** @var \Illuminate\Database\Eloquent\Builder $all_products */
        $all_products = prepareFilteredListingsNew($request, $categoryGroup->categories);

        $priceRange = compact('min', 'max');

        // Paginate the results
        $products = $all_products->paginate(config('system.view_listing_per_page', 16))
            ->appends($request->except('page'));

        return view('theme::category_group', compact('categoryGroup', 'products', 'priceRange'));
    }

    /**
     * Retrieves and displays the details of a product based on its slug.
     *
     * @param string $slug The slug of the product.
     * @return \Illuminate\View\View The view displaying the product details.
     */
    public function product($slug)
    {
        $item = Inventory::where('slug', $slug)->withCount('feedbacks')->available()->withTrashed()->first();

        if (!$item) {
            return view('theme::exceptions.item_not_available');
        }

        $item->load([
            'product' => function ($q) use ($item) {
                $q->select('id', 'brand', 'model_number', 'mpn', 'gtin', 'gtin_type', 'origin_country', 'slug', 'description', 'downloadable', 'manufacturer_id', 'sale_count', 'created_at')
                    ->withCount(['inventories' => function ($query) use ($item) {
                        $query->where('shop_id', '!=', $item->shop_id)->available();
                    }]);
            },
            'attributeValues' => function ($q) {
                $q->select('id', 'attribute_values.attribute_id', 'value', 'color', 'order')
                    ->with('attribute:id,name,attribute_type_id,order');
            },
            'shop' => function ($q) {
                $q->withCount('inventories')
                    ->with([
                        'avgFeedback:rating,count,feedbackable_id,feedbackable_type',
                        'latestFeedbacks' => function ($q) {
                            $q->with('customer:id,nice_name,name');
                        },
                    ]);
            },
            'latestFeedbacks' => function ($q) {
                $q->with('customer:id,nice_name,name');
            },
            'avgFeedback:rating,count,feedbackable_id,feedbackable_type',
            'images:id,path,imageable_id,imageable_type',
            'tags:id,name',
        ]);

        // Auction listings
        if (is_incevio_package_loaded('auction')) {
            $item->loadCount('bids');
        }

        $this->update_recently_viewed_items($item); //update_recently_viewed_items

        $variants = ListHelper::variants_of_product($item, $item->shop_id);

        $attr_pivots = DB::table('attribute_inventory')
            ->select('attribute_id', 'inventory_id', 'attribute_value_id')
            ->whereIn('inventory_id', $variants->pluck('id'))->get();

        $item_attrs = $attr_pivots->where('inventory_id', $item->id)
            ->pluck('attribute_value_id')->toArray();

        $attributes = Attribute::select('id', 'name', 'attribute_type_id', 'order')
            ->whereIn('id', $attr_pivots->pluck('attribute_id'))
            ->with(['attributeValues' => function ($query) use ($attr_pivots) {
                $query->whereIn('id', $attr_pivots->pluck('attribute_value_id'))->orderBy('order');
            }])
            ->orderBy('order')->get();

        $related = ListHelper::related_products($item);
        $linked_items = ListHelper::linked_items($item);
        $alternative_items = ListHelper::alternative_items($item);

        // Country list for ship_to dropdown
        $business_areas = Cache::rememberForever('countries_cached', function () {
            return Country::select('id', 'name', 'iso_code')->orderBy('name', 'asc')->get();
        });

        if (is_incevio_package_loaded('wholesale')) {
            $item->wholesale_prices = get_wholesale_item_prices($item->id);
        }

        return view('theme::product', compact('item', 'variants', 'attributes', 'item_attrs', 'related', 'linked_items', 'business_areas', 'alternative_items'));
    }

    /**
     * Open product quick review modal
     *
     * @param  string  $slug
     * @return \Illuminate\View\View| string HTML of rendered view
     */
    public function quickViewItem($slug)
    {
        $item = Inventory::where('slug', $slug)
            ->available()
            ->with([
                'images:path,imageable_id,imageable_type',
                'product' => function ($q) {
                    $q->select('id', 'slug', 'downloadable')
                        ->withCount(['inventories' => function ($query) {
                            $query->available();
                        }]);
                },
            ])
            ->withCount('feedbacks')->firstOrFail();

        if (is_incevio_package_loaded('wholesale')) {
            $item->wholesale_prices = get_wholesale_item_prices($item->id);
        }

        $this->update_recently_viewed_items($item); // update recently viewed items

        $variants = ListHelper::variants_of_product($item, $item->shop_id);

        $attr_pivots = DB::table('attribute_inventory')
            ->select('attribute_id', 'inventory_id', 'attribute_value_id')
            ->whereIn('inventory_id', $variants->pluck('id'))->get();

        $attributes = Attribute::select('id', 'name', 'attribute_type_id', 'order')
            ->whereIn('id', $attr_pivots->pluck('attribute_id'))
            ->with([
                'attributeValues' => function ($query) use ($attr_pivots) {
                    $query->whereIn('id', $attr_pivots->pluck('attribute_value_id'))->orderBy('order');
                },
            ])
            ->orderBy('order')->get();

        return view('theme::modals.quickview', compact('item', 'attributes'))->render();
    }

    /**
     * Open shop page
     *
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function offers($slug)
    {
        $product = Product::where('slug', $slug)
            ->with([
                'inventories' => function ($q) {
                    $q->available();
                },
                'inventories.attributeValues.attribute',
                'inventories.avgFeedback:rating,count,feedbackable_id,feedbackable_type',
                'inventories.shop.feedbacks:rating,feedbackable_id,feedbackable_type',
                'inventories.shop.image:path,imageable_id,imageable_type',
            ])
            ->firstOrFail();

        return view('theme::offers', compact('product'));
    }

    /**
     * Open brand list page
     *
     * @return \Illuminate\View\View
     */
    public function all_brands()
    {
        $brands = Manufacturer::select('id', 'slug', 'name')->active()->with('logoImage')->paginate(24);

        return view('theme::brand_lists', compact('brands'));
    }

    /**
     * Open brand page
     *
     * @param BrowseProductRequest $request
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function brand(BrowseProductRequest $request, $slug)
    {
        $now = Carbon::now();
        $brand = Manufacturer::where('slug', $slug)->firstOrFail();
        $ids = Product::where('manufacturer_id', $brand->id)->pluck('id');

        $all_products = Inventory::whereIn('product_id', $ids)
            //->filter($request->all())
            ->whereHas('shop', function (\Illuminate\Database\Eloquent\Builder $q) {
                $q->select(['id', 'current_billing_plan', 'active'])->active();
            })
            ->with([
                'avgFeedback:rating,count,feedbackable_id,feedbackable_type',
                'images:path,imageable_id,imageable_type',
            ])
            ->where('parent_id', null)
            ->active()/*->inRandomOrder()->get()*/;

        $forPriceRange = $all_products->get();
        $min = floor($forPriceRange->min('sale_price'));
        $max = ceil($forPriceRange->max('sale_price'));
        $priceRange = compact('min', 'max');

        // Filtering occurs after priceRange has been extracted.
        if ($request->sort_by) {
            $all_products = $all_products->filter($request->all())->get();
        } else {
            $all_products = $all_products->filter($request->all())->inRandomOrder()->get();
        }

        // Paginate the results
        $products = $all_products->paginate(16);  // PLS 15 -> 16 products per page (4 rows by 4 products)

        return view('theme::brand', compact('brand', 'products', 'priceRange'));
    }

    /**
     * Open brand page (theme 2.8.2)
     * @param BrowseProductRequest $request
     * @param  string  $slug
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\View\View
     */
    public function brandProducts(BrowseProductRequest $request, string $slug)
    {
        $now = Carbon::now();

        $brand = Manufacturer::where('slug', $slug)->firstOrFail();
        $ids = Product::where('manufacturer_id', $brand->id)->pluck('id');

        $all_products = Inventory::whereIn('product_id', $ids)
            ->groupBy('product_id', 'shop_id')
            ->whereHas('shop', function (\Illuminate\Database\Eloquent\Builder $q) {
                $q->select(['id', 'current_billing_plan', 'active'])->active();
            })
            ->with([
                'avgFeedback:rating,count,feedbackable_id,feedbackable_type',
                'images:path,imageable_id,imageable_type',
            ])
            ->withCount([
                'orders' => function ($q) {
                    $q->where('order_items.created_at', '>=', Carbon::now()->subHours(config('system.popular.hot_item.period', 24)));
                },
            ])
            ->active()/*->inRandomOrder()->get()*/;

        $forPriceRange = $all_products->get();
        $min = floor($forPriceRange->min('sale_price'));
        $max = ceil($forPriceRange->max('sale_price'));
        $priceRange = compact('min', 'max');

        // Filtering occurs after priceRange has been extracted.
        if ($request->sort_by) {
            $all_products = $all_products->filter($request->all())->get();
        } else {
            $all_products = $all_products->filter($request->all())->inRandomOrder()->get();
        }

        // Paginate the results
        $products = $all_products->paginate(16);  // PLS 15 -> 16 products per page (4 rows by 4 products)

        return view('theme::brand', compact('brand', 'products', 'priceRange'));
    }

    /**
     * Display the category list page.
     * @return \Illuminate\View\View
     */
    public function categories()
    {
        return view('theme::categories');
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function openPage($slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();

        return view('theme::page', compact('page'));
    }

    /**
     * Push product ID to session for the recently viewed items section
     *
     * @param  [type] $item [description]
     */
    private function update_recently_viewed_items($item)
    {
        $items = Session::get('products.recently_viewed_items', []);

        if (!in_array($item->getKey(), $items)) {
            Session::push('products.recently_viewed_items', $item->getKey());
        } else {
            $key = array_search($item->getKey(), $items);

            unset($items[$key]);

            Session::push('products.recently_viewed_items', $item->getKey());
        }

        Cache::forget('recently_viewed_items');
    }
}
