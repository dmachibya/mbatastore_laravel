<?php

namespace App\Models;

use App\Common\Translatable;
use Carbon\Carbon;
use App\Common\Taggable;
use App\Common\Imageable;
use App\Common\Attachable;
use App\Common\Feedbackable;
use Laravel\Scout\Searchable;
use EloquentFilter\Filterable;
// use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Common\CascadeSoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Inventory extends Inspectable
{
    use HasFactory, SoftDeletes, CascadeSoftDeletes, Taggable, Imageable, Searchable, Filterable, Feedbackable, Attachable, Translatable;

    const CONDITIONS = ['New', 'Used', 'Refurbished'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'inventories';

    /**
     * Cascade Soft Deletes Relationships
     *
     * @var array
     */
    protected $cascadeDeletes = ['carts'];

    /**
     * The attributes that should be mutated to dates. (as carbon instances)
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be casted to boolean types.
     *
     * @var array
     */
    protected $casts = [
        'offer_start' => 'datetime',
        'offer_end' => 'datetime',
        'available_from' => 'datetime',
        'expiry_date' => 'datetime',
        'auction_end' => 'datetime',
        'free_shipping' => 'boolean',
        'stuff_pick' => 'boolean',
        'auctionable' => 'boolean',
        'active' => 'boolean',
    ];

    /**
     * The attributes that should be inspectable for restricted keywords.
     *
     * @var array
     */
    protected static $inspectable = [
        'title',
        'condition_note',
        'description',
        'key_features',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id',
        'shop_id',
        'title',
        'warehouse_id',
        'product_id',
        'brand',
        'supplier_id',
        'sku',
        'condition',
        'condition_note',
        'description',
        'download_limit',
        'key_features',
        'stock_quantity',
        'sold_quantity',
        'damaged_quantity',
        'user_id',
        'purchase_price',
        'sale_price',
        'offer_price',
        'offer_start',
        'offer_end',
        'shipping_weight',
        'length',
        'width',
        'height',
        'free_shipping',
        'stuff_pick',
        'available_from',
        'expiry_date',
        'min_order_quantity',
        'linked_items',
        'slug',
        'meta_title',
        'meta_description',
        'active',
        'auctionable',
        'auction_status',
        'base_price',
        'auction_end',
        'bid_accept_action',
        'affiliate_commission_percentage',
        'credit_back_percentage',
        'shopify_id',
    ];

    /**
     * The boot method for the Inventory model.
     *
     * This method is called when the Inventory model is being booted.
     * It adds a global scope to the model to include translations based on the current locale.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::addGlobalScope('withTranslations', function (Builder $builder) {
            $builder->with(['translations' => function ($query) {
                $query->where('lang', app()->getLocale())->whereNotNull('translation');
            }]);
        });
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $key_features = $this->key_features;

        if ($key_features && is_serialized($key_features)) {
            $key_features = implode(', ', unserialize($this->key_features));
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'brand' => $this->brand,
            'description' => str_replace('-', '‑', strip_tags($this->description)),
            'stock_quantity' => $this->stock_quantity,
            'sale_price' => $this->sale_price,
            'sku' => $this->sku,
            'key_features' => $this->key_features ?? null,
            'attributes' => $this->attributeValues,
            'shop' => $this->shop->name ?? null,
            'shop_id' => $this->shop_id,
            'product' => $this->product->name,
            'product_id' => $this->product->id,
            'product_gtin' => $this->product->gtin,
            'active' => $this->active,
            'available_from' => $this->available_from,
            'expiry_date' => $this->expiry_date,
            'created_at' => $this->created_at,
        ];
    }

    /**
     * Modify the query used to retrieve models when making all of the models searchable.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function makeAllSearchableUsing($query)
    {
        return $query->with(['product', 'shop', 'attributeValues']);
    }

    /**
     * Get the shop of the inventory.
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Get the Warehouse associated with the inventory.
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the product of the inventory.
     */
    public function product()
    {
        return $this->belongsTo(Product::class)->withDefault();
    }

    /**
     * Get the variants of the item.
     */
    public function variants()
    {
        return $this->hasMany(Inventory::class, 'parent_id')->withTrashed();
    }

    public function buyerGroupDetails()
    {
        return $this->hasMany(\Incevio\Package\BuyerGroup\Models\InventoryBuyerGroupDetail::class);
    }

    /**
     * Get the supplier for the inventory.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class)->withDefault();
    }

    /**
     * Get the Inventory's translations
     */
    public function translations()
    {
        return $this->hasMany(InventoryTranslation::class);
    }

    /**
     * Get the packagings for the order.
     */
    public function packagings()
    {
        return $this->belongsToMany(\Incevio\Package\Packaging\Models\Packaging::class)
            ->withTimestamps();
    }

    /**
     * Get the Attributes for the inventory.
     */
    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'attribute_inventory')
            ->withPivot('attribute_value_id')->withTimestamps();
    }

    /**
     * Get the attribute values that owns the SubGroup.
     */
    public function attributeValues()
    {
        return $this->belongsToMany(AttributeValue::class, 'attribute_inventory')
            ->withPivot('attribute_id')->withTimestamps();
    }

    /**
     * Get the carts for the product.
     */
    public function carts()
    {
        return $this->belongsToMany(Cart::class, 'cart_items')
            ->withPivot('item_description', 'quantity', 'unit_price')->withTimestamps();
    }

    /**
     * Get the orders for the product.
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items')
            ->withPivot('item_description', 'quantity', 'unit_price', 'feedback_id')
            ->withTimestamps();
    }

    /**
     * Get the bids from the auction listing
     * @return HasMany
     */
    public function bids(): HasMany
    {
        return $this->hasMany(\Incevio\Package\Auction\Models\Bid::class)
            ->orderBy('amount_in_system_currency', 'desc');
    }

    /**
     * Get the user's bid for the auction listing
     * @return HasOne
     */
    public function mybid(): HasOne
    {
        return $this->hasOne(\Incevio\Package\Auction\Models\Bid::class)
            ->where('customer_id', Auth::guard('customer')->id());
    }

    /**
     * Get the top bid for the auction listing
     * @return HasOne
     */
    public function topbid(): HasOne
    {
        return $this->hasOne(\Incevio\Package\Auction\Models\Bid::class)
            ->ofMany('amount_in_system_currency', 'max');
    }

    /**
     * Get condition_note attribute after translation
     */
    public function getConditionNoteAttribute($value)
    {
        return $this->translateAttribute('condition_note') ?? $value;
    }

    /**
     * Get description attribute after translation
     */
    public function getDescriptionAttribute($value)
    {
        return $this->translateAttribute('description') ?? $value;
    }

    /**
     * Get key_features attribute after translation
     */
    public function getKeyFeaturesAttribute($value)
    {
        return $this->translateAttribute('key_features') ?? $value;
    }

    /**
     * Get the wholesalePrices for the order.
     */
    public function wholesalePrices(): HasMany
    {
        return $this->hasMany(\Incevio\Package\WholeSale\Models\WholeSalePrice::class, 'inventory_id');
    }

    public function affiliateLinks(): HasMany
    {
        return $this->hasMany(\Incevio\Package\Affiliate\Models\AffiliateLink::class);
    }

    /**
     * Get the user has affiliate link for the listing
     * @return HasOne
     */
    public function myLink(): HasOne
    {
        return $this->hasOne(\Incevio\Package\Affiliate\Models\AffiliateLink::class)
            ->where('affiliate_id', Auth::guard('affiliate')->id());
    }

    public function affiliateCommission(): HasMany
    {
        return $this->hasMany(\Incevio\Package\Affiliate\Models\AffiliateCommission::class);
    }

    /**
     * Get the manufacturer associated with the product.
     */
    public function getManufacturerAttribute()
    {
        return $this->product->manufacturer;
    }

    /**
     * Get action status of the inventory
     */
    public function getAuctionStatusTextAttribute()
    {
        if (!$this->auctionable) {
            return '';
        }

        if (!$this->auction_status == \Incevio\Package\Auction\Enums\AuctionStatusEnum::SUSPENDED) {
            return trans('packages.auction.auction_suspended');
        }

        if (!$this->auction_status == \Incevio\Package\Auction\Enums\AuctionStatusEnum::PAUSED) {
            return trans('packages.auction.auction_paused');
        }

        if (!$this->auction_end) {
            return trans('app.not_set');
        }

        if ($this->available_from->isFuture()) {
            return trans('app.scheduled');
        }

        if ($this->auction_end->isFuture()) {
            return trans('packages.auction.auction_running');
        }

        if ($this->auction_end->isPast()) {
            return trans('app.ended');
        }

        return trans('app.unknown');
    }

    /**
     * Get action status of the inventory
     * 
     * @return boolean
     */
    public function isAuctionRunning()
    {
        return $this->auction_end && $this->available_from->isPast() && $this->auction_end->isFuture();
    }

    /**
     * Check if the quantity is getting lower that the alert quantity
     *
     * @return boolean
     */
    public function isLowQtt()
    {
        $alert_quantity = config('shop_settings.alert_quantity') ?? 0;

        return $this->stock_quantity <= $alert_quantity;
    }

    /**
     * Check if the is out of stock
     *
     * @return boolean
     */
    public function isOutOfStock()
    {
        return $this->stock_quantity < $this->min_order_quantity;
    }

    /**
     * Check if the item has a valid offer price.
     * Check if the item has a valid offer price.
     * 
     * @return boolean
     */
    public function hasOffer()
    {
        if (
            ($this->offer_price > 0) &&
            ($this->offer_price < $this->sale_price) &&
            ($this->offer_start < Carbon::now()) &&
            ($this->offer_end > Carbon::now())
        ) {
            return true;
        }

        return false;
    }

    /**
     * Check if the inventory has affiliate commission.
     *
     * @return bool 
     */
    public function hasAffiliateCommission()
    {
        return $this->affiliates_percentage > 0;
    }

    /**
     * Get the minimum order quantity for the buyer group of the inventory.
     *
     * @return int|null The minimum order quantity, or null if the buyer group price is not available for the current customer.
     */
    public function buyerGroupMinOrderQuantity()
    {
        if (customerHasGroupPricing()) {
            return \Incevio\Package\BuyerGroup\Models\InventoryBuyerGroupDetail::where('inventory_id', $this->id)
                ->where('buyer_group_id', Auth::guard('customer')->user()->buyer_group_id)
                ->value('min_order_quantity');
        }

        return null;
    }

    /**
     * Check if the model is active.
     *
     * @return bool
     */
    public function isActive()
    {
        if ($this->active != static::ACTIVE) {
            return false;
        }

        if (is_incevio_package_loaded('pharmacy')) {
            return $this->expiry_date > Carbon::now();
        }

        return true;
    }

    /**
     * Check if the item is in flash deals
     *
     * @return boolean
     */
    public function isInDeals()
    {
        $flashdeals = get_flash_deals();

        if (isset($flashdeals['listings']) && $flashdeals['listings']->find($this->id)) {
            return true;
        }

        return false;
    }

    /**
     * Check if the product was imported from Shopify
     *
     * @return bool
     */
    public function isFromShopify()
    {
        return (bool) $this->shopify_id;
    }

    /**
     * Return current sale price
     * Return current sale price
     *
     * @return number
     */
    public function current_sale_price()
    {
        if ($this->auctionable) {
            return $this->base_price;
        }

        return $this->hasOffer() ? $this->offer_price : $this->sale_price;
    }

    /**
     * Return the discount percentage
     *
     * @return number
     */
    public function discount_percentage()
    {
        return $this->hasOffer() ? get_percentage_of($this->sale_price, $this->offer_price) : 0;
    }

    /**
     * Get the affiliates percentage attribute. Return default shop affiliate commission percentage if null.
     *
     * @return float The affiliate commission percentage.
     */
    public function getAffiliatesPercentageAttribute()
    {
        return is_null($this->affiliate_commission_percentage) ? getShopConfig($this->shop_id, 'default_affiliate_commission_percentage') : $this->affiliate_commission_percentage;
    }

    /**
     * Return a nice amount value with text with % symbol 
     *
     * @return float
     */
    public function getAffiliateCommissionPercentageTextAttribute()
    {
        $value = $percentage = 0;
        if (is_incevio_package_loaded('affiliate')) {
            $percentage = $this->affiliates_percentage ? $this->affiliates_percentage : 0;

            $value = ($this->current_sale_price() / 100) * $percentage;
        }

        return get_formated_currency($value, 2) . " ({$percentage}%)";
    }

    /**
     * Return the reward percentage value
     *
     * @return float|integer
     */
    public function getRewardPercentageAttribute()
    {
        $value = 0;
        if (is_incevio_package_loaded('wallet') && is_wallet_credit_reward_enabled()) {
            if ($this->credit_back_percentage !== null) {
                $value = $this->credit_back_percentage;
            } else {
                $value = getShopConfig($this->shop_id, 'credit_back_percentage');
            }
        }

        return get_formated_decimal($value, true, 2);
    }

    /**
     * Return the reward amount value
     *
     * @return float|integer
     */
    public function getRewardAmountAttribute()
    {
        $value = 0;
        if (is_incevio_package_loaded('wallet') && is_wallet_credit_reward_enabled()) {
            if ($this->credit_back_percentage !== null) {
                $value = $this->credit_back_percentage;
            } else {
                $value = getShopConfig($this->shop_id, 'credit_back_percentage');
            }
        }

        return get_formated_decimal($value, true, 2);
    }

    /**
     * Setters
     */
    public function setMinOrderQuantityAttribute($value)
    {
        $this->attributes['min_order_quantity'] = $value > 1 ? $value : 1;
    }

    public function setOfferPriceAttribute($value)
    {
        $this->attributes['offer_price'] = $value > 0 ? $value : null;
    }

    public function setWarehouseIdAttribute($value)
    {
        $this->attributes['warehouse_id'] = $value > 0 ? serialize($value) : null;
    }

    public function getWarehouseIdAttribute($value)
    {
        // To ensure compatibility with previous versions that didn't store warehouse_id as serialized
        return is_serialized($value) ? unserialize($value) : $value;
    }

    public function setSupplierIdAttribute($value)
    {
        $this->attributes['supplier_id'] = $value > 0 ? $value : null;
    }

    public function setAvailableFromAttribute($value)
    {
        if ($value) {
            $this->attributes['available_from'] = Carbon::createFromFormat('Y-m-d h:i a', $value);
        }
    }

    public function setOfferStartAttribute($value)
    {
        if ($value) {
            $this->attributes['offer_start'] = Carbon::createFromFormat('Y-m-d h:i a', $value);
        }
    }

    public function setOfferEndAttribute($value)
    {
        if ($value) {
            $this->attributes['offer_end'] = Carbon::createFromFormat('Y-m-d h:i a', $value);
        }
    }

    public function setExpiryDateAttribute($value)
    {
        if ($value) {
            $this->attributes['expiry_date'] = date('Y-m-d', strtotime($value));
        }
    }

    public function setFreeShippingAttribute($value)
    {
        $this->attributes['free_shipping'] = (bool) $value;
    }

    public function setKeyFeaturesAttribute($value)
    {
        $t_key_features = null;

        if (is_array($value)) {
            $value = array_filter($value, function ($item) {
                return !empty($item[0]);
            });
        }

        if ($value) {
            $t_key_features = is_serialized($value) ? $value : serialize($value);
        }

        $this->attributes['key_features'] = $t_key_features;
    }

    public function setLinkedItemsAttribute($value)
    {
        $this->attributes['linked_items'] = (bool) $value ? serialize($value) : null;
    }

    /**
     * Getters
     */
    public function getPackagingListAttribute()
    {
        if (count($this->packagings)) {
            return $this->packagings->pluck('id')->toArray();
        }
    }

    public function getExpiryDateAttribute($value)
    {
        if ($value) {
            return date('Y-m-d', strtotime($value));
        }
    }

    public function getSalePriceAttribute($value)
    {
        if (customerHasGroupPricing()) {
            $customerGroupPrice = \Incevio\Package\BuyerGroup\Models\InventoryBuyerGroupDetail::where('inventory_id', $this->id)
                ->where('buyer_group_id', Auth::guard('customer')->user()->buyer_group_id)
                ->value('sale_price');

            return $customerGroupPrice ?? $value;
        }

        return $value;
    }

    public function getMinOrderQuantityAttribute($value)
    {
        if (customerHasGroupPricing()) {
            return $this->buyerGroupMinOrderQuantity() ?? $value;
        }

        return $value;
    }

    // This Mutators causes the searse error
    // public function getKeyFeaturesAttribute($value)
    // {
    //     return unserialize($value);
    // }
    // public function getLinkedItemsAttribute($value)
    // {
    //     return unserialize($value);
    // }

    /**
     * Scope a query to only include available for sale .
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAvailable($query)
    {
        // return $query;

        // return $query->where([
        //     ['active', '=', 1],
        //     ['stock_quantity', '>', 0],
        //     ['available_from', '<=', Carbon::now()]
        // ]);

        $query = $query->whereHas('shop', function ($q) {
            $q->active();
        })->where([
            ['active', '=', 1],
            // ['stock_quantity', '>', 0],
            ['available_from', '<=', Carbon::now()],
        ])->zipcode();

        // Hide out-of-stock items when enabled
        if (config('system_settings.hide_out_of_stock_items')) {
            $query = $query->where('stock_quantity', '>', 0);
        }

        // Check expiry date when pharmacy plugin is enabled
        if (is_incevio_package_loaded('pharmacy')) {
            $query = $query->where('expiry_date', '>', Carbon::now());
        }

        return $query;
    }

    /**
     * Scope a query to only include available for sale .
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasOffer($query)
    {
        return $query->where([
            ['offer_price', '>', 0],
            ['offer_start', '<', Carbon::now()],
            ['offer_end', '>', Carbon::now()],
        ])->whereColumn('offer_price', '<', 'sale_price');
    }

    /**
     * Scope a query to only include items with free Shipping.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFreeShipping($query)
    {
        return $query->where('free_shipping', 1);
    }

    /**
     * Scope a query to only include new Arrival Items.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNewArraivals($query)
    {
        return $query->where('inventories.created_at', '>', Carbon::now()->subDays(config('system.filter.new_arraival', 7)));
    }

    /**
     * Scope a query to only include low qtt items.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLowQtt($query)
    {
        $alert_quantity = config('shop_settings.alert_quantity') ?? 0;

        return $query->where('stock_quantity', '<=', $alert_quantity);
    }

    /**
     * Scope a query to only include out of stock items.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStockOut($query)
    {
        return $query->where('stock_quantity', '<=', 0);
    }

    /**
     * Scope a query to only include auction items.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAuction($query)
    {
        return $query->where('auctionable', 1);
    }

    /**
     * Scope a query to only include non auction items.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBuyNow($query)
    {
        return $query->where('auctionable', 0);
    }

    /**
     * Scope a query to only include active records.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        $query = $query->where([
            ['active', '=', static::ACTIVE],
            ['stock_quantity', '>', 0],
            ['available_from', '<=', Carbon::now()],
        ]);

        // Check expiry date when pharmacy plugin is enabled
        if (is_incevio_package_loaded('pharmacy')) {
            $query = $query->where('expiry_date', '>', Carbon::now());
        }

        return $query;
    }

    /**
     * Scope a query to only include inactive records.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInActive($query)
    {
        $query = $query->where('active', '!=', static::ACTIVE)
            ->orWhere('available_from', '>', Carbon::now());

        if (is_incevio_package_loaded('pharmacy')) {
            $query = $query->orWhere(
                function ($q) {
                    $q->whereNull('expiry_date')
                        ->orWhere('expiry_date', '<', Carbon::now());
                }
            );
        }

        return $query;
    }

    /** if zipcode package active this function will return zipcode query
     * @param $query
     * @return mixed
     */
    public function scopeZipcode($query): object
    {
        if (is_incevio_package_loaded('zipcode')) {
            return $query->whereHas('shop.address', function ($builder) {
                return $builder->where('zip_code', session('zipcode_default'));
            });
        }

        return $query;
    }

    /**
     * Check if the item is an hot item
     *
     * @return bool
     */
    public function isHotItem()
    {
        return $this->orders_count >= config('system.popular.hot_item.sell_count', 3);
    }

    /**
     * Returns if the item is in stock
     *
     * @return bool
     */
    public function getAvailabilityAttribute()
    {
        return $this->stock_quantity > 0 ? trans('theme.in_stock') : trans('theme.out_of_stock');
    }

    /**
     * Return the total stocks qtt including the sold items
     *
     * @return int
     */
    public function getTotalStockAttribute()
    {
        return $this->stock_quantity + $this->sold_quantity;
    }

    /**
     * Return formatted title attribute
     *
     * @param $value
     * @return string
     * @return string
     */
    public function getTitleAttribute($value)
    {
        $value = $this->translateAttribute('title') ?? $value;

        return str_replace("'", '', $value);
    }

    /**
     * Get the type for the item.
     *
     * @return array
     */
    public function getTypeAttribute()
    {
        return $this->product->downloadable ? trans('app.digital') : trans('app.physical');
    }

    /**
     * Returns translated name of condition
     *
     * @return string condition
     */
    public function getConditionAttribute($condition)
    {
        // Skip the mutator when editing the model
        if (Route::currentRouteName() == 'admin.stock.inventory.edit') {
            return $condition;
        }

        switch ($condition) {
            case 'New':
                return trans('app.new');
            case 'Used':
                return trans('app.used');
            case 'Refurbished':
                return trans('app.refurbished');
        }
    }

    /**
     * Returns translated label text
     *
     * @return array labels
     */
    public function getLabels()
    {
        $labels = [];

        if ($this->isHotItem()) {
            $labels[] = trans('theme.hot_item');
        }

        if ($this->free_shipping) {
            $labels[] = trans('theme.free_shipping');
        }

        if ($this->stuff_pick) {
            $labels[] = trans('theme.stuff_pick');
        }

        if ($this->hasOffer()) {
            $labels[] = trans('theme.percent_off', ['value' => $this->discount_percentage()]);
        }

        if ($this->auctionable) {
            $labels[] = trans('packages.auction.auction');
        }

        return $labels;
    }

    /**
     * Translate given attributes value from translation_inventories table
     *
     * @param string $attribute - attribute name to translate
     * @return string - translated value of the attribute
     */
    public function translateAttribute(string $attribute)
    {
        if (Route::currentRouteName() == 'admin.stock.inventory.edit') {
            return null;
        }

        $inventory_translation = $this->translations->first();

        if (!$inventory_translation || !isset($inventory_translation->translation[$attribute])) {
            return null;
        }

        return $attribute == 'key_features' ? serialize($inventory_translation->translation[$attribute]) : $inventory_translation->translation[$attribute];
    }
}
