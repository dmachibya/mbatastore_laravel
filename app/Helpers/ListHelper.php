<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Models\Blog;
use App\Models\Page;
use App\Models\Role;
use App\Models\Shop;
use App\Models\User;
use App\Models\Order;
use App\Models\Module;
use App\Models\Refund;
use App\Models\Ticket;
use App\Models\Dispute;
use App\Models\Message;
use App\Models\Product;
use App\Enums\GtinTypes;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Language;
use App\Models\Supplier;
use App\Models\Attribute;
use App\Models\BaseModel;
use App\Models\Inventory;
use App\Models\Permission;
use App\Models\PdfTemplate;
use App\Models\Manufacturer;
use App\Models\CategoryGroup;
use App\Models\PaymentMethod;
use App\Models\ShippingMethod;
use App\Models\CategorySubGroup;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

/**
 * This is a helper class to process,upload and remove images from different models
 */
class ListHelper
{
    public static function common_select_attr($model = 'inventory')
    {
        switch ($model) {
            case 'inventory':
                $fields = ['inventories.id', 'inventories.shop_id', 'title', 'inventories.product_id', 'sku', 'condition', 'stock_quantity', 'min_order_quantity', 'sold_quantity', 'available_from', 'sale_price', 'offer_price', 'offer_start', 'offer_end', 'free_shipping', 'slug', 'stuff_pick'];

                // Pharmacy fields
                if (is_incevio_package_loaded('pharmacy')) {
                    $fields = array_merge($fields, ['expiry_date']);
                }

                // Auction fields
                if (is_incevio_package_loaded('auction')) {
                    $fields = array_merge($fields, ['auctionable', 'auction_status', 'base_price', 'auction_end']);
                }

                return $fields;
        }

        return '*';
    }

    /**
     * Get PDF Templet Types list for form dropdown.
     *
     * @return array
     */
    public static function getTempletTypes()
    {
        return  [
            PdfTemplate::TYPE_ORDER_INVOICE => trans('app.order_invoice_pdf_template'),
            PdfTemplate::TYPE_SHIPPING_LABEL => trans('app.shipping_label'),
            // PdfTemplate::TYPE_WALLET_TRANSACTION => trans('app.wallet_transaction'),
            // PdfTemplate::TYPE_AFFILIATE_WALLET_TRANSACTION => trans('app.affiliate_transaction'),
        ];
    }

    /**
     * Get shipping_method_types list for form dropdown.
     *
     * @return array
     */
    public static function shipping_method_types()
    {
        return  [
            ShippingMethod::TYPE_MANUAL      => trans('app.shipping_method_type.manual.name'),
            ShippingMethod::TYPE_ONLINE      => trans('app.shipping_method_type.online.name'),
        ];
    }

    /**
     * Get payment_method_types list for form dropdown.
     *
     * @return array
     */
    public static function payment_method_types()
    {
        return  [
            PaymentMethod::TYPE_PAYPAL      => trans('app.payment_method_type.paypal.name'),
            PaymentMethod::TYPE_CREDIT_CARD => trans('app.payment_method_type.credit_card.name'),
            PaymentMethod::TYPE_MANUAL      => trans('app.payment_method_type.manual.name'),
            PaymentMethod::TYPE_OTHERS      => trans('app.payment_method_type.others.name'),
            PaymentMethod::MOBILE_WALLET    => trans('app.payment_method_type.mobile_wallet.name'),
        ];
    }

    public static function payment_statuses()
    {
        return  [
            Order::PAYMENT_STATUS_UNPAID    => trans('app.statuses.unpaid'),
            Order::PAYMENT_STATUS_PENDING   => trans('app.statuses.pending'),
            Order::PAYMENT_STATUS_PAID      => trans('app.statuses.paid'),
        ];
    }

    public static function order_statuses()
    {
        return  [
            Order::STATUS_WAITING_FOR_PAYMENT   =>  trans('app.statuses.waiting_for_payment'),
            Order::STATUS_PAYMENT_ERROR         =>  trans('app.statuses.payment_error'),
            Order::STATUS_CONFIRMED             =>  trans('app.statuses.confirmed'),
            // Order::STATUS_FULFILLED             =>  trans("app.statuses.fulfilled"),
            Order::STATUS_AWAITING_DELIVERY     =>  trans('app.statuses.awaiting_delivery'),
            Order::STATUS_DELIVERED             =>  trans('app.statuses.delivered'),
            Order::STATUS_RETURNED              =>  trans('app.statuses.refunded'),
        ];
    }

    public static function gerder_list()
    {
        $gerder_list = [
            'app.male' => trans('app.male'),
            'app.female' => trans('app.female')
        ];

        if (!config('system.disable_other_gender')) {
            $gerder_list['app.other'] = trans('app.other');
        }

        return $gerder_list;
    }

    public static function item_conditions()
    {
        return [
            'New' => trans('app.new'),
            'Used' => trans('app.used'),
            'Refurbished' => trans('app.refurbished')
        ];
    }

    public static function ticket_priorities()
    {
        return  [
            Ticket::PRIORITY_LOW      => trans('app.priorities.low'),
            Ticket::PRIORITY_NORMAL   => trans('app.priorities.normal'),
            Ticket::PRIORITY_HIGH     => trans('app.priorities.high'),
            Ticket::PRIORITY_CRITICAL => trans('app.priorities.critical'),
        ];
    }

    public static function ticket_statuses_new()
    {
        return  [
            Ticket::STATUS_NEW      => trans('app.statuses.new'),
            Ticket::STATUS_OPEN     => trans('app.statuses.open'),
            Ticket::STATUS_PENDING  => trans('app.statuses.pending'),
        ];
    }

    public static function ticket_statuses_all()
    {
        return  self::ticket_statuses_new() + [
            Ticket::STATUS_CLOSED   => trans('app.statuses.closed'),
            Ticket::STATUS_SOLVED   => trans('app.statuses.solved'),
            Ticket::STATUS_SPAM     => trans('app.statuses.spam'),
        ];
    }

    /**
     * Get dispute statuses list for form dropdown.
     *
     * @return array
     */
    public static function dispute_statuses()
    {
        if (Auth::user() instanceof Customer) {
            $statuses = [
                Dispute::STATUS_OPEN     => trans('app.statuses.open'),
                Dispute::STATUS_SOLVED   => trans('app.statuses.solved'),
            ];
        } else {
            $statuses = [
                Dispute::STATUS_NEW      => trans('app.statuses.new'),
                Dispute::STATUS_OPEN     => trans('app.statuses.open'),
                Dispute::STATUS_WAITING  => trans('app.statuses.waiting'),
                Dispute::STATUS_SOLVED   => trans('app.statuses.solved'),
                Dispute::STATUS_CLOSED   => trans('app.statuses.closed'),
            ];
        }

        if (!Auth::user() instanceof Customer && auth()->user()->isFromPlatform()) {
            $statuses[Dispute::STATUS_APPEALED] = trans('app.statuses.appealed');
        }

        return $statuses;
    }

    /**
     * Get refund statuses list for form dropdown.
     *
     * @return array
     */
    public static function refund_statuses()
    {
        return  [
            Refund::STATUS_NEW      => trans('app.statuses.new'),
            Refund::STATUS_APPROVED  => trans('app.statuses.approved'),
            Refund::STATUS_DECLINED => trans('app.statuses.declined'),
        ];
    }

    /**
     * Get fulfilment types list for form dropdown.
     *
     * @return array
     */
    public static function fulfilment_types()
    {
        return [
            Order::FULFILMENT_TYPE_PICKUP => trans('app.fulfilment_type.pickup'),
            Order::FULFILMENT_TYPE_DELIVER => trans('app.fulfilment_type.deliver'),
            Order::FULFILMENT_TYPE_POS => trans('app.fulfilment_type.pos'),
        ];
    }

    public static function marketplace_business_area()
    {
        return  [
            '1' => trans('app.worldwide'),
            '0' => trans('app.active_business_area'),
        ];
    }

    public static function faq_topics_for()
    {
        return  [
            'Merchant'    => trans('app.merchants'),
            'Customer'    => trans('app.customers'),
        ];
    }

    /**
     * Get page positions list for form dropdown.
     *
     * @return array
     */
    public static function page_positions()
    {
        return  [
            'copyright_area'    => trans('app.copyright_area'),
            'footer_1st_column' => trans('app.footer_1st_column'),
            'footer_2nd_column' => trans('app.footer_2nd_column'),
            'footer_3rd_column' => trans('app.footer_3rd_column'),
            'none'              => trans('app.none'),
            'main_nav'           => trans('app.main_nav'),
        ];
    }

    /**
     * Get system timezone.
     *
     * @return Collection|null
     */
    public static function system_timezone()
    {
        if (config('system_settings.timezone_id')) {
            return DB::table('timezones')->where('id', config('system_settings.timezone_id'))->first();
        }

        return null;
    }

    public static function ticket_categories()
    {
        return DB::table('ticket_categories')->orderBy('name', 'asc')->pluck('name', 'id');
    }

    public static function plans()
    {
        $plans = DB::table('subscription_plans')
            ->where('deleted_at', Null)
            ->orderBy('order', 'asc')
            ->select('plan_id', 'name', 'cost')
            ->get();

        $result = [];
        foreach ($plans as $plan) {
            $result[$plan->plan_id] = $plan->name . ' (' . get_formated_currency($plan->cost, 2) . trans('app.per_month') . ')';
        }

        return $result;
    }

    /**
     * Get unread Messages.
     *
     * @return array
     */
    public static function unreadMessages()
    {
        $query = Message::with('customer.avatarImage')->labelOf(Message::LABEL_INBOX)->unread();

        if (Auth::user()->isFromMerchant()) {
            $query = $query->where('shop_id', Auth::user()->shop_id);
        } else {
            $query = $query->where('user_id', Auth::user()->id);
        }

        return $query->get();
    }

    /**
     * Get role list for form dropdown.
     * If the logged in user from a shop then show return roles thats are public.
     * otherwise return roles thats not public
     *
     * @return Collection
     */
    public static function roles()
    {
        $roles = Role::lowerPrivileged();

        if (Auth::user()->isFromPlatform()) {
            $roles->whereNull('shop_id')->notPublic();
        } else {
            $roles->orWhere(
                function ($query) {
                    $query->whereNull('shop_id')->where('public', 1);
                }
            );
        }

        return $roles->where('id', '!=', Role::MERCHANT)->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get list of all available languages formatted for dropdown list
     */
    public static function availableLocales()
    {
        return Cache::rememberForever('active_locales', function () {
            return Language::orderBy('order', 'asc')->active()->get();
        });
    }

    public static function availableTranslationLocales()
    {
        return Language::orderBy('order', 'asc')
            ->where('code', '!=', config('system_settings.default_language'))
            ->active()->get();
    }

    /**
     * Get list of all available categories formatted for theme
     *
     * @return array
     */
    public static function categoriesForTheme($all = false)
    {
        return Cache::rememberForever('all_categories', function () use ($all) {
            $result = CategoryGroup::select('id', 'name', 'slug', 'icon')
                ->with([
                    'logoImage:id,path,imageable_id,imageable_type',
                    'backgroundImage:id,path,imageable_id,imageable_type',
                    'subGroups' => function ($query) use ($all) {
                        $query->select('id', 'slug', 'category_group_id', 'name');

                        if (!$all) {
                            $query->active()->has('categories.products.inventories');
                        }

                        $query->orderBy('categories_count', 'desc')->withCount('categories');
                    },
                    'subGroups.categories' => function ($q) use ($all) {
                        $q->select('id', 'category_sub_group_id', 'name', 'slug', 'description');

                        if (!$all) {
                            $q->active()->has('products.inventories');
                        }
                    },
                ]);

            if (!$all) {
                $result->has('subGroups.categories.products.inventories')->active();
            }

            return $result->orderBy('order', 'asc')->get();
        });
    }

    /**
     * Get list of all available category group
     *
     * @return Collection
     */
    public static function categoryGrps()
    {
        return DB::table('category_groups')->where('deleted_at', null)->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get list of category sub-group
     *
     * @return Collection
     */
    public static function catSubGrps()
    {
        return CategorySubGroup::orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get list of category sub-group under the given category
     *
     * @return Collection
     */
    public static function thisCatSubGrps($category)
    {
        return DB::table('category_sub_groups')->where('deleted_at', null)
            ->where('category_group_id', $category)->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get categories list for form dropdown.
     *
     * @return array
     */
    public static function categories()
    {
        return Cache::rememberForever('category_list_for_form', function () {
            return DB::table('categories')->whereNull('deleted_at')->pluck('name', 'id');
        });
    }

    /**
     * Get search_categories list for form dropdown.
     *
     * @return array
     */
    public static function search_categories()
    {
        return Cache::remember('search_category_list', config('cache.remember.categories', 0), function () {
            return CategoryGroup::select('id', 'name', 'slug')
                ->with(['subGroups' => function ($q) {
                    $q->select('name', 'slug', 'category_group_id')->active();
                }])
                ->whereHas('subGroups', function ($q) {
                    $q->active();
                })
                ->orderBy('order', 'desc')
                ->active()->get();
        });
    }

    /**
     * Get all catGrpSubGrpListArray
     *
     * @return array
     */
    public static function catGrpSubGrpListArray()
    {
        $groups = [];
        foreach (self::categoryGrps() as $key => $value) {
            $list = [];

            foreach (self::thisCatSubGrps($key) as $key2 => $value2) {
                $list[$key2] = $value2;
            }

            if (count($list)) {
                $groups[$value] = $list;
            }
        }

        return $groups;
    }

    /**
     * Get all catWithSubGrpList
     *
     * @return array
     */
    public static function catWithSubGrpListArray()
    {
        $categoryGroups = CategoryGroup::select(['id', 'name'])->active()
            ->orderBy('name', 'asc')
            ->with([
                'subGroups' => function ($q) {
                    $q->select(['id', 'name', 'category_group_id'])->orderBy('name', 'asc')->active();
                },
                'subGroups.categories' => function ($q) {
                    $q->select(['id', 'category_sub_group_id', 'name'])->active();
                },
            ])->get();

        $grps = [];
        foreach ($categoryGroups as $categoryGroup) {
            foreach ($categoryGroup->subGroups as $categorySubGroup) {
                $list = [];

                foreach ($categorySubGroup->categories as $category) {
                    $list[$category->id] = $category->name;
                }

                if (count($list)) {
                    $grps[$categoryGroup->name . ' &#9656; ' . $categorySubGroup->name . ' &#9662;'] = $list;
                }
            }
        }

        return $grps;
    }

    /**
     * Get permissions list for form dropdown.
     *
     * @return array
     */
    public static function permissions()
    {
        return Permission::orderBy('module_id', 'asc')->pluck('name', 'id');
    }

    /**
     * Get modulesWithPermissions list.
     *
     * @return Collection
     */
    public static function modulesWithPermissions()
    {
        return Module::active()->with('permissions')->orderBy('name', 'asc')->get();
    }

    /**
     * Get array of slugsWithModulAccess list.
     *
     * @return array
     */
    public static function slugsWithModulAccess()
    {
        return Permission::with('module')->get()->pluck('module.access', 'slug')->toArray();
    }

    /**
     * Get Popular_blogs list.
     *
     * @return Collection
     */
    public static function popularBlogs()
    {
        return Blog::select(['id', 'title', 'slug', 'excerpt', 'published_at'])->popular()->take(5)->get();
    }

    /**
     * Get latest_blogs list.
     *
     * @return Collection
     */
    public static function recentBlogs()
    {
        return Blog::select(['id', 'title', 'slug', 'excerpt', 'published_at'])
            ->published()->recent()->take(5)->get();
    }

    /**
     * Get users list for form dropdown.
     *
     * @return Collection
     */
    public static function users()
    {
        return DB::table('users')->where('deleted_at', null)->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get users list for form dropdown.
     *
     * @return Collection
     */
    public static function platform_users()
    {
        return DB::table('users')->where('shop_id', null)->where('role_id', '!=', 3)
            ->where('deleted_at', null)->orderBy('name', 'asc')->pluck('name', 'id');
    }

    public static function shipping_zones($shop = null)
    {
        $shop = $shop ?? Auth::user()->merchantId(); //Get current user's shop_id

        return DB::table('shipping_zones')
            ->where('shop_id', $shop)
            ->where('active', BaseModel::ACTIVE)
            ->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get permission slugs for the user role.
     *
     * @return array
     */
    public static function authorizations(User $user = null)
    {
        $user = $user ?? Auth::guard('web')->user(); // Get current user

        if (!$user->role_id || $user->isSuperAdmin()) {
            return [];
        }

        return $user->role->permissions()->pluck('slug')->toArray();
    }

    /**
     * Get all FAQ topic list for form dropdown.
     *
     * @return Collection
     */
    public static function faq_topics()
    {
        return DB::table('faq_topics')->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * [open_tickets description]
     *
     * @return [type] [description]
     */
    public static function open_tickets()
    {
        return Ticket::open()->orderBy('priority', 'desc')->with('category')
            ->withCount('replies')->latest()->limit(10)->get();
    }

    /**
     * [top_customers description]
     *
     * @return [type] [description]
     */
    public static function top_customers($limit = 5)
    {
        return Customer::select('id', 'nice_name', 'name', 'email')
            ->with('image:path,imageable_id,imageable_type', 'orders:id,customer_id,total')
            ->whereHas('orders', function ($query) {
                $query->select('customer_id', 'shop_id', 'total')->withArchived();

                if (Auth::user()->merchantId()) {
                    $query->mine();
                }
            })
            ->withCount(['orders' => function ($q) {
                $q->withArchived();
                if (Auth::user()->merchantId()) {
                    $q->mine();
                }
            }])
            ->orderBy('orders_count', 'desc')
            ->limit($limit)->get();
    }

    /**
     * [returning_customers description]
     *
     * @return [type] [description]
     */
    public static function returning_customers($limit = 5)
    {
        $customers = static::top_customers($limit);

        // Return customer has more than one orders
        return $customers->filter(function ($customer, $key) {
            return $customer->orders->count() > 1;
        });
    }

    /**
     * [top_vendors description]
     *
     * @return [type] [description]
     */
    public static function top_vendors($limit = 5)
    {
        return Shop::select('id', 'owner_id', 'name', 'slug')
            ->with('logoImage:path,imageable_id,imageable_type', 'revenue')
            ->withCount('inventories')
            ->active()
            ->take($limit)
            ->get()
            ->sortByDesc('revenue');
    }

    /**
     * Return unique brand names from the given listings
     *
     * @return array
     */
    public static function get_unique_brand_names_from_listings($listings)
    {
        return $listings->whereNotNull('brand')->pluck('brand')->unique();
    }

    /**
     * Get all merchants list for form dropdown.
     *
     * @return Collection
     */
    public static function merchants()
    {
        return DB::table('users')->where('role_id', 3)->where('deleted_at', null)
            ->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get new merchants list for form dropdown.
     *
     * @return Collection
     */
    public static function new_merchants()
    {
        return DB::table('users')->whereNull('shop_id')->whereNull('deleted_at')
            ->where('role_id', Role::MERCHANT)->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get users list under the shop for form dropdown.
     *
     * @return Collection
     */
    public static function staffs($shop = null)
    {
        $shop = $shop ?? Auth::user()->merchantId(); //Get current user's shop_id

        return DB::table('users')->where('shop_id', $shop)->where('deleted_at', null)
            ->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get suppliers list for form dropdown.
     *
     * @return Collection
     */
    public static function suppliers($shop = null)
    {
        $shop = $shop ?? Auth::user()->merchantId(); // Get current user's shop_id

        return DB::table('suppliers')->where('shop_id', $shop)
            ->where('deleted_at', null)
            ->where('active', BaseModel::ACTIVE)
            ->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get timezones list for form dropdown.
     *
     * @return Collection
     */
    public static function timezones()
    {
        return DB::table('timezones')->pluck('text', 'id');
    }

    public static function languages()
    {
        return DB::table('languages')->where('deleted_at', null)
            ->where('active', BaseModel::ACTIVE)
            ->orderBy('order', 'asc')->pluck('language', 'code');
    }

    /**
     * Get warehouses list for form dropdown.
     *
     * @return Collection
     */
    public static function warehouses($shop_id = null)
    {
        $shop_id = $shop_id ?? Auth::user()->merchantId();

        return DB::table('warehouses')
            ->where('shop_id', $shop_id)
            ->where('deleted_at', null)
            ->where('active', BaseModel::ACTIVE)
            ->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get carriers list for form dropdown.
     *
     * @return Collection
     */
    public static function carriers($shop_id = null)
    {
        $shop_id = $shop_id ?? Auth::user()->merchantId();

        return DB::table('carriers')->where('shop_id', $shop_id)
            ->where('deleted_at', null)
            ->where('active', BaseModel::ACTIVE)
            ->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get taxes list for form dropdown.
     *
     * @return Collection
     */
    public static function taxes()
    {
        return DB::table('taxes')->where('active', BaseModel::ACTIVE)
            ->where('deleted_at', null)
            ->where(function ($query) {
                $query->where('public', 1)
                    ->orWhere('shop_id', Auth::user()->merchantId());
            })
            ->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get customers list for form dropdown.
     * @return Collection
     */
    public static function customers()
    {
        return DB::table('customers')->where('deleted_at', null)
            ->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get inventories list for form dropdown.
     * @return Collection
     */
    public static function inventories($shop = null)
    {
        $shop = $shop ?? Auth::user()->merchantId();

        return DB::table('inventories')->where('shop_id', $shop)->where('deleted_at', null)
            ->where('parent_id', null)->orderBy('title', 'asc')->pluck('title', 'id');
    }

    /**
     * Get top listing_items list for merchant.
     *
     * @param [type] $shop
     * @param integer $count between 5 and 100
     *
     * @return Collection
     */
    public static function top_listing_items($shop = null, $count = 5)
    {
        $count = $count < 5 ? 5 : ($count > 100 ? 100 : $count);

        if (Auth::user()->isFromMerchant()) {
            $shop = Auth::user()->merchantId();
        }

        return Inventory::where('inventories.shop_id', $shop)
            ->with('image:path,imageable_id,imageable_type', 'attributeValues:id,value')
            ->select(
                'inventories.id',
                'inventories.shop_id',
                'inventories.title',
                'inventories.stock_quantity',
                'inventories.sku',
                'inventories.active',
                'products.name',
                'inventories.product_id',
                DB::raw('SUM(order_items.quantity) as sold_qtt'),
                DB::raw('SUM(order_items.unit_price) as gross_sales')
            )
            ->join('products', 'inventories.product_id', 'products.id')
            ->join('order_items', 'inventories.id', 'order_items.inventory_id')
            ->groupBy('inventory_id')
            ->orderBy('sold_qtt', 'desc')
            ->limit($count)->get();
    }

    /**
     * Get top categories list for merchant.
     * @return Collection
     */
    public static function top_categories($count = 5)
    {
        return Category::select('id', 'slug', 'name', 'active')
            ->whereHas('listings', function ($query) {
                $query->mine();
            })
            ->withCount('listings')
            ->orderBy('listings_count', 'desc')
            ->limit($count)->get();
    }

    /**
     * Get top suppliers list for merchant.
     * @return Collection
     */
    public static function top_suppliers($count = 5)
    {
        return Supplier::select('id', 'shop_id', 'name', 'active')->mine()
            ->with('image:path,imageable_id,imageable_type')
            ->withCount('inventories')
            ->orderBy('inventories_count', 'desc')->limit($count)->get();
    }

    /**
     * Get trending items list. Get the most ordered item in given days
     * @return Collection
     */
    public static function popular_items($days = 7, $limit = 15, $shop_id = null)
    {
        $items = Inventory::available()
            ->select(static::common_select_attr('inventory'))
            ->whereNull('parent_id')
            ->with([
                'avgFeedback:rating,count,feedbackable_id,feedbackable_type',
                'image:path,imageable_id,imageable_type',
            ]);

        if ($shop_id) {
            $items = $items->where('shop_id', $shop_id);
        }

        // It's a trick for the demo only to get different items
        if (config('app.demo') == true) {
            return $items->limit(99)->get()->random(5);
        }

        $from = Carbon::today()->subDays($days)->startOfDay();

        return $items->withCount([
            'orders' => function ($q) use ($from) {
                $q->withArchived()->where('orders.created_at', '>', $from);
            },
        ])
            ->orderBy('orders_count', 'desc')
            ->limit($limit)->get();
    }

    /**
     * Get featured products list for dropdown
     * @return array|null
     */
    public static function featured_items($shop_id = null)
    {
        if ($items = get_from_option_table('featured_items' . $shop_id, [])) {
            return DB::table('inventories')->whereIn('id', $items)
                ->orderBy('title', 'asc')->pluck('title', 'id')->toArray();
        }

        return null;
    }

    /**
     * Get featured brands list for dropdown
     * @return array
     */
    public static function featured_brands()
    {
        if ($brands = get_featured_brand_ids()) {
            return DB::table('manufacturers')->whereIn('id', $brands)
                ->orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        }

        return [];
    }

    /**
     * Get featured vendors list for dropdown
     * @return array
     */
    public static function featured_vendors()
    {
        if ($vendors = get_featured_vendor_ids()) {
            return DB::table('shops')->whereIn('id', $vendors)
                ->orderBy('name', 'asc')->pluck('name', 'id')->toArray();
        }

        return [];
    }

    /**
     * Get Featured Brands
     * @return array|Collection
     */
    public static function get_featured_brands()
    {
        if ($featured_brands = get_from_option_table('featured_brands', [])) {
            return Manufacturer::whereIn('id', $featured_brands)->get();
        }

        return [];
    }

    /**
     * Get Best Finds under Defined Price
     * @return Collection
     */
    public static function best_find_under($price, $limit = 20, $shop_id = null)
    {
        if (!$price) {
            return collect([]);
        }

        $items = Inventory::available()
            ->select(static::common_select_attr('inventory'))
            ->with([
                'avgFeedback:rating,count,feedbackable_id,feedbackable_type',
                'image:path,imageable_id,imageable_type',
            ])
            ->whereNull('parent_id')
            ->where(function ($q) use ($price) {
                $q->where('sale_price', '<=', $price)
                    ->orWhere(function ($q) use ($price) {
                        $q->hasOffer()->where('offer_price', '<=', $price);
                    });
            });

        if ($shop_id) {
            $items = $items->where('shop_id', $shop_id);
        }

        return $items->inRandomOrder()->take($limit)->get();
    }

    /**
     * Get latest_products list
     *
     * @return array
     */
    public static function latest_products()
    {
        return Cache::remember('latest_products_dashboard', 7200, function () {
            return Product::with('featureImage')
                ->withSum('inventories', 'sold_quantity')
                ->latest()->limit(8)->get();
        });
    }

    /**
     * Get top_selling_products list
     *
     * @return array
     */
    public static function top_selling_products()
    {
        return Cache::remember('top_selling_products_dashboard', 7200, function () {
            return Product::with('featureImage')
                ->withSum('inventories', 'sold_quantity')
                ->orderByDesc('inventories_sum_sold_quantity')
                ->limit(8)
                ->get();
        });
    }

    /**
     * Get top_brand_products list
     *
     * @return array
     */
    public static function top_selling_brands()
    {
        return Cache::remember('top_selling_brands_dashboard', 7200, function () {
            return Manufacturer::with(['logoImage', 'country:id,name'])
                ->withSum('inventories', 'sold_quantity')
                ->orderByDesc('inventories_sum_sold_quantity')
                ->limit(8)
                ->get();
        });
    }

    /**
     * Get top_brand_products list
     *
     * @return array
     */
    public static function top_selling_categories()
    {
        return Cache::remember('top_selling_categories_dashboard', 7200, function () {
            return Category::with('featureImage', 'subGroup.group')
                ->withSum('listings', 'sold_quantity')
                ->orderByDesc('listings_sum_sold_quantity')
                ->limit(8)
                ->get();
        });
    }

    /**
     * Get latest products that has live listing
     * @return array
     */
    public static function latest_available_items($limit = 10, $shop_id = null)
    {
        return Cache::remember('latest_items', config('cache.remember.latest_items', 0), function () use ($shop_id, $limit) {
            return Inventory::available()
                ->select(static::common_select_attr('inventory'))
                ->whereNull('parent_id')
                ->with([
                    'avgFeedback:rating,count,feedbackable_id,feedbackable_type',
                    'image:path,imageable_id,imageable_type',
                ])
                ->when($shop_id, function ($query, $shop_id) {
                    return $query->where('shop_id', $shop_id);
                })
                ->zipcode()->where('parent_id', null)
                ->latest()->limit($limit)->get();
        });
    }

    /**
     * Get latest digital products that has live listing
     * @return array
     */
    public static function latest_digital_items($limit = 10, $shop_id = null)
    {
        return Cache::remember('latest_items', config('cache.remember.latest_items', 0), function () use ($shop_id, $limit) {
            return Inventory::available()
                ->select(static::common_select_attr('inventory'))
                ->whereNull('parent_id')
                ->with([
                    'avgFeedback:rating,count,feedbackable_id,feedbackable_type',
                    'image:path,imageable_id,imageable_type',
                ])
                ->when($shop_id, function ($query, $shop_id) {
                    return $query->where('shop_id', $shop_id);
                })
                ->whereHas('product', function ($query) {
                    $query->where('downloadable', true);
                })
                ->zipcode()->where('parent_id', null)
                ->latest()->limit($limit)->get();
        });
    }

    /**
     * Get latest products of given shop
     * @return array
     */
    public static function latest_shop_items(Shop $shop, $limit = 10)
    {
        return Cache::remember('latest_items_' . $shop->slug, config('cache.remember.latest_items', 0), function () use ($limit, $shop) {
            return Inventory::available()
                ->select(static::common_select_attr('inventory'))
                ->where('shop_id', $shop->id)
                ->whereNull('parent_id')
                ->with([
                    'avgFeedback:rating,count,feedbackable_id,feedbackable_type',
                    'image:path,imageable_id,imageable_type',
                ])
                ->groupBy('product_id')
                ->latest()->limit($limit)->get();
        });
    }

    /**
     * Get latest products of given shop
     * @return array
     */
    public static function top_selling_shop_items(Shop $shop, $limit = 10)
    {
        return Cache::remember('top_selling_items_' . $shop->slug, config('cache.remember.latest_items', 0), function () use ($limit, $shop) {
            return Inventory::available()
                ->select(static::common_select_attr('inventory'))
                ->where('shop_id', $shop->id)
                ->whereNull('parent_id')
                ->with([
                    'avgFeedback:rating,count,feedbackable_id,feedbackable_type',
                    'image:path,imageable_id,imageable_type',
                ])
                ->groupBy('product_id')
                ->orderBy('sold_quantity', 'desc')->limit($limit)->get();
        });
    }

    /**
     * Get variants of product of given item
     * @return Collection
     */
    public static function variants_of_product($item, $shop = null)
    {
        $variants = Inventory::available()
            ->select(static::common_select_attr('inventory'))
            ->where('product_id', $item->product_id)
            ->where('stock_quantity', '>', 0);

        if ($shop) {
            $variants = $variants->where('shop_id', $shop);
        }

        return $variants->with([
            'images:path,imageable_id,imageable_type',
            'attributeValues:id,value,color',
        ])->get();
    }

    /**
     * Get related products of given item
     * @return collection
     */
    public static function related_products($item, $limit = 10)
    {
        $catIds = $item->product->categories->pluck('id');

        $productIDs = DB::table('category_product')
            ->whereIn('category_id', $catIds)
            ->inRandomOrder()
            ->limit($limit * 2)
            ->pluck('product_id')->toArray();

        if (empty($productIDs)) {
            return collect([]);
        }

        return Inventory::whereIn('product_id', $productIDs)->available()
            ->select(static::common_select_attr('inventory'))
            ->with([
                'avgFeedback:rating,count,feedbackable_id,feedbackable_type',
                'image:path,imageable_id,imageable_type',
            ])
            ->inRandomOrder()
            ->where('parent_id', null)
            ->limit($limit)->get();
    }

    /**
     * Get linked items of given item
     * @return collection
     */
    public static function linked_items($item)
    {
        $linked_items = unserialize($item->linked_items);

        if (empty($linked_items)) {
            return collect([]);
        }

        $items = Inventory::whereIn('id', $linked_items)->available()
            ->select(static::common_select_attr('inventory'))
            ->with([
                'avgFeedback:rating,count,feedbackable_id,feedbackable_type',
                'image:path,imageable_id,imageable_type',
            ])
            ->where('parent_id', null)
            ->get();

        return $items;
    }

    /**
     * Get alternative items for a given item.
     *
     * @param  mixed  $item  The item for which alternative items are needed.
     * @return \Illuminate\Database\Eloquent\Collection  The collection of alternative items.
     */
    public static function alternative_items($item, $limit = 10)
    {
        return Inventory::where('product_id', $item->product_id)
            ->where('id', '!=', $item->id)
            ->available()
            ->select(static::common_select_attr('inventory'))
            ->with([
                'avgFeedback:rating,count,feedbackable_id,feedbackable_type',
                'image:path,imageable_id,imageable_type',
            ])
            ->inRandomOrder()
            ->where('parent_id', null)
            ->limit($limit)
            ->get();
    }

    /**
     * Get given number of random products
     * @return array
     */
    public static function random_items($limit = null)
    {
        return Cache::remember('random_items', config('cache.remember.random_items', 0), function () use ($limit) {
            $items = Inventory::available()
                ->select(static::common_select_attr('inventory'))
                ->with([
                    'avgFeedback:rating,count,feedbackable_id,feedbackable_type',
                    'image:path,imageable_id,imageable_type',
                ])
                ->where('parent_id', null)
                ->inRandomOrder('id');

            if ($limit) {
                return $items->limit($limit)->get();
            }

            return $items->simplePaginate(config('mobile_app.view_listing_per_page', 8));
        });
    }

    public static function recentlyViewedItems()
    {
        return Cache::rememberForever('recently_viewed_items', function () {
            $products = Session::get('products.recently_viewed_items');

            if (!$products) {
                return collect([]);
            } else {
                $products = array_reverse($products);  //To get recent viewed product id as desc order
            }

            return Inventory::whereIn('id', $products)->available()
                ->select(static::common_select_attr('inventory'))
                ->whereNull('parent_id')
                ->with([
                    'avgFeedback:rating,count,feedbackable_id,feedbackable_type',
                    'image:path,imageable_id,imageable_type',
                ])->orderByRaw("FIELD(id, " . implode(',', $products) . ")")  // To get data order by products id whatever has like [2,5,3,7]
                ->get();
        });
    }

    /**
     * Get orders list for form dropdown.
     * @return array
     */
    public static function orders()
    {
        return Order::mine()->orderBy('order_number', 'asc')
            ->pluck('order_number', 'id')->toArray();
    }

    /**
     * Get latest_orders list for merchant.
     *
     * @return Collection
     */
    public static function latest_orders($limit = 10)
    {
        if ($limit < 5) {
            $limit = 5;
        } elseif ($limit > 100) {
            $limit = 100;
        }

        return Order::mine()->with('customer')->latest()->limit($limit)->get();
    }

    /**
     * Get paid_orders list for form dropdown.
     *
     * @return array
     */
    public static function paid_orders()
    {
        $query = DB::table('orders')->where('payment_status', Order::PAYMENT_STATUS_PAID)
            ->where('deleted_at', null);

        if (!Auth::user()->isFromPlatform()) {
            $query = $query->where('shop_id', Auth::user()->merchantId());
        }

        return $query->orderBy('order_number', 'asc')
            ->pluck('order_number', 'id')->toArray();
    }

    /**
     * Get latest_stocks list for merchant.
     *
     * @return Collection
     */
    public static function latest_stocks()
    {
        return Inventory::mine()->with('product', 'image:path,imageable_id,imageable_type')
            ->whereNull('parent_id')
            ->latest()->limit(10)->get();
    }

    /**
     * Get low_qtt_stocks list for merchant.
     *
     * @return array
     */
    public static function low_qtt_stocks()
    {
        return Inventory::mine()->lowQtt()
            ->with('product', 'image:path,imageable_id,imageable_type')
            ->latest()->limit(10)->get();
    }

    /**
     * Get address_types list for form dropdown.
     *
     * @return Collection
     */
    public static function address_types()
    {
        return DB::table('address_types')->orderBy('id', 'asc')->pluck('type', 'type');
    }

    /**
     * Get packagings list for form dropdown.
     *
     * @return Collection
     */
    public static function packagings()
    {
        return DB::table('packagings')
            ->where('shop_id', Auth::user()->merchantId())
            ->where('active', BaseModel::ACTIVE)
            ->where('deleted_at', null)
            ->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get country list for form dropdown.
     *
     * @return Collection
     */
    public static function active_business_areas()
    {
        $countries = DB::table('countries');

        if (!config('system_settings.worldwide_business_area')) {
            $countries->where('active', BaseModel::ACTIVE);
        }

        return $countries->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get country list for form dropdown.
     *
     * @return Collection
     */
    public static function countries()
    {
        return DB::table('countries')->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get states list for form dropdown.
     *
     * @param  int $country_id
     *
     * @return Collection
     */
    public static function states($country_id = null)
    {
        $country_id = $country_id ?? config('system_settings.address_default_country');

        return DB::table('states')->where('country_id', $country_id)
            ->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get currency list for form dropdown.
     *
     * @return array
     */
    public static function currencies($all = false)
    {
        $query = DB::table('currencies')->select('name', 'symbol', 'iso_code', 'id');

        if (!$all) {
            $query->where('active', BaseModel::ACTIVE);
        }

        $currencies = $query->orderBy('priority', 'asc')->orderBy('name', 'asc')->get();

        $result = [];
        foreach ($currencies as $currency) {
            $result[$currency->id] = $currency->name . ' (' . $currency->iso_code . ' ' . $currency->symbol . ')';
        }

        return $result;
    }

    /**
     * Get attributes list for form dropdown.
     *
     * @return array
     */
    public static function attributes($all = false)
    {
        $query = DB::table('attributes')->where('deleted_at', null);

        if (!$all) {
            $query->where('shop_id', Auth::user()->merchantId());
        }

        return $query->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get attributes list with all values for form dropdown.
     *
     * @return Collection
     */
    public static function attributeWithValues()
    {
        return Attribute::where('deleted_at', null)
            ->with('attributeValues')->orderBy('order', 'asc')->get();
    }

    /**
     * Get attribute_types list for form dropdown.
     *
     * @return Collection
     */
    public static function attribute_types()
    {
        return DB::table('attribute_types')->orderBy('type', 'asc')->pluck('type', 'id');
    }

    /**
     * Get manufacturers list for form dropdown.
     *
     * @return Collection
     */
    public static function manufacturers()
    {
        return DB::table('manufacturers')->where('deleted_at', null)
            ->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get gtin_types list for form dropdown.
     *
     * @return Collection
     */
    public static function gtin_types()
    {
        return GtinTypes::list();
    }

    /**
     * Get EmailTemplate list with all values for form dropdown.
     *
     * @return Collection
     */
    public static function email_templates()
    {
        $query = DB::table('email_templates')->where('deleted_at', null);

        if (Auth::user()->isFromPlatform()) {
            $query->whereNull('shop_id')
                ->where('template_for', 'Platform');
        } else {
            $query->where('shop_id', Auth::user()->merchantId())
                ->orWhere('template_for', 'Merchant');
        }

        return $query->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get banner_groups list for form dropdown.
     *
     * @return Collection
     */
    public static function banner_groups()
    {
        $query = DB::table('banner_groups')->orderBy('name', 'asc');

        if (Auth::user()->isFromMerchant()) {
            $query->take(2);
        }

        return $query->pluck('name', 'id');
    }

    /**
     * Get featured_categories list for form dropdown.
     *
     * @return object
     */
    public static function featured_categories()
    {
        return DB::table('categories')->whereNull('deleted_at')
            ->where('featured', true)->pluck('name', 'id');
    }

    /**
     * Return the list of trending categories in array
     *
     * @return array
     */
    public static function trending_categories()
    {
        $trending_ids = get_trending_category_ids();

        if ($trending_ids) {
            return [];
        }

        return Category::whereIn('id', $trending_ids)->get()->pluck('name', 'id')->toArray();
    }

    /**
     * Return the deal of the day item
     *
     * @return Inventory | null
     */
    public static function deal_of_the_day($shop_id = null)
    {
        $item = get_from_option_table('deal_of_the_day' . $shop_id);

        return Inventory::where('id', $item)->first();
    }

    /**
     * Get pages list for theme.
     */
    public static function pages($visibility = null)
    {
        return Cache::rememberForever('cached_pages', function () use ($visibility) {
            if ($visibility) {
                return Page::select('title', 'slug', 'position')
                    ->published()->visibilityOf($visibility)->get();
            }

            return Page::select('title', 'slug', 'position')->published()->get();
        });
    }

    /**
     * Get shops list for form dropdown.
     *
     * @return array
     */
    public static function shops()
    {
        return Shop::approved()->orderBy('name', 'asc')->pluck('name', 'id')->toArray();
    }

    /**
     * Get tags list for form dropdown.
     *
     * @return Collection
     */
    public static function tags()
    {
        return DB::table('tags')->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Get list of the delivery boys of the vendor
     *
     * @return Collection
     */
    public static function deliveryBoys($shop_id = null)
    {
        $shop_id = $shop_id ?? Auth::user()->merchantId();

        return DB::table('delivery_boys')
            ->where('shop_id', $shop_id)
            ->where('status', BaseModel::ACTIVE)
            ->orderBy('nice_name', 'asc')
            ->pluck('nice_name', 'id');
    }

    /**
     * Return attribute list for the given product
     *
     * @param Product $product
     * @return Collection
     */
    public static function getAttributesBy(Product $product)
    {
        if ($attrs = $product->categories->pluck('attrsList')) {
            return $attrs->flatten()->unique('id');
        }

        return $attrs;
    }

    /**
     * Return attribute list for the given product
     *
     * @param Product $product
     * @return array
     */
    public static function product_attributes(Product $product)
    {
        $attrs = $product->categories->pluck('attrsList');

        return self::get_attr_list_with_values($attrs->flatten()->unique('id'));
    }

    /**
     * Return attribute list for the given category
     *
     * @param Category $category
     * @return array
     */
    public static function category_attributes(Category $category)
    {
        return self::get_attr_list_with_values($category->attrsList);
    }

    /**
     * prepare the list of attribute and include all the values of the attributes
     *
     * @param mix $attrs
     * @return array
     */
    // Required PHP 8.1 to support multiple type cast
    // public static function get_attr_list_with_values(Collection|EloquentCollection $attrs)
    public static function get_attr_list_with_values($attrs)
    {
        $result = [];

        if ($ids = $attrs->pluck('id')) {
            $values = DB::table('attribute_values')
                ->select('id', 'value', 'color', 'attribute_id', 'order')
                ->whereIn('attribute_id', $ids)
                ->orderBy('order')->get();

            foreach ($attrs as $attr) {
                $result[$attr->id] = [
                    'attribute' => $attr->name,
                    'values' => $values->where('attribute_id', $attr->id)->pluck('value', 'id')
                ];
            }
        }

        return $result;
    }

    /**
     * Return formatted weekdays
     *
     * @return array
     */
    public static function business_days()
    {
        return [
            'Sat' => 'Saturday',
            'Sun' => 'Sunday',
            'Mon' => 'Monday',
            'Tues' => 'Tuesday',
            'Wed' => 'Wednesday',
            'Thurs' => 'Thursday',
            'Fri' => 'Friday'
        ];
    }

    /**
     * Subscription plans list array
     *
     * @return array
     */
    public static function subscriptionPlans()
    {
        return DB::table('subscription_plans')->orderBy('order', 'asc')
            ->pluck('plan_id', 'name')->toArray();
    }

    /**
     * List of the navigation items
     *
     * @return array
     */
    public static function navigations()
    {
        $navigations = [
            "Categories" => "Categories",
            "Brands" => "Brands",
            "Vendors" => "Vendors",
            "Sale" => "Sale",
        ];

        if (is_incevio_package_loaded('eventy')) {
            $navigations["events"] = "Events";
        }

        if (is_incevio_package_loaded('auction')) {
            $navigations["auction"] = "Auction Products";
        }

        return $navigations;
    }

    /**
     * Return smart form list
     *
     * @return array
     */
    public static function smart_forms(): array
    {
        if (is_incevio_package_loaded('smartForm')) {
            $list = DB::table('smart_forms')->orderBy('name', 'asc')->pluck('name', 'id')->toArray();

            $list[null] = trans('app.placeholder.select'); // To avoid force select

            return $list;
        }

        return [];
    }
}
