<?php

namespace App\Models;

use App\Common\Billable;
use App\Common\Taggable;
use App\Common\Imageable;
use App\Common\Attachable;
use App\Common\Addressable;
use App\Common\ApiAuthTokens;
use Laravel\Scout\Searchable;
use App\Common\HasHumanAttributes;
use Illuminate\Support\Facades\Hash;
// use Spatie\Activitylog\Traits\HasActivity;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Laravel\Passport\HasApiTokens;

// Uncomment below line to enable Wallet plugin. (Have to install the plugin.)
use App\Notifications\Auth\CustomerResetPasswordNotification;

class Customer extends CustomerWallet
{
    use HasFactory, SoftDeletes, HasHumanAttributes, Billable, Notifiable, Addressable, Taggable, Imageable, Attachable, Searchable, ApiAuthTokens;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'customers';

    /**
     * The guard used by the model.
     *
     * @var string
     */
    protected $guard = 'customer';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verification_token',
    ];

    /**
     * The attributes that should be casted to boolean types.
     *
     * @var array
     */
    protected $casts = [
        'last_visited_at' => 'datetime',
        'dob' => 'date',
        'active' => 'boolean',
        'approval_status' => 'boolean',
        'accepts_marketing' => 'boolean',
        'phone_verified_at' => 'datetime',
    ];

    /**
     * The attributes that will be logged on activity logger.
     *
     * @var bool
     */
    protected static $logFillable = true;

    /**
     * The only attributes that has been changed.
     *
     * @var bool
     */
    protected static $logOnlyDirty = true;

    /**
     * The name that will be used when log this model. (optional)
     *
     * @var bool
     */
    protected static $logName = 'customer';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'nice_name',
        'email',
        'phone',
        'phone_verified',
        'password',
        'dob',
        'sex',
        'description',
        'stripe_id',
        'card_holder_name',
        'card_brand',
        'card_last_four',
        'active',
        'approval_status',
        'remember_token',
        'verification_token',
        'accepts_marketing',
        'fcm_token',

        // for buyer group
        'buyer_group_id',
        'buyer_group_requested_id',
        'buyer_group_application_status',
        'buyer_group_application_details',

        // For smartForm data
        'extra_info',
    ];

    /**
     * Get the value used to index the model.
     *
     * @return mixed
     */
    public function getScoutKey()
    {
        return $this->email;
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $searchable = [];
        $searchable['id'] = $this->id;
        $searchable['name'] = $this->name;
        $searchable['nice_name'] = $this->nice_name;
        $searchable['email'] = $this->email;
        $searchable['description'] = str_replace('-', '‑', strip_tags($this->description));
        $searchable['dob'] = $this->dob;
        $searchable['active'] = $this->active;

        return $searchable;
    }

    /**
     * Route notifications for the mail channel.
     *
     * @return string
     */
    public function routeNotificationForMail()
    {
        return $this->email;
    }

    /**
     * Route notifications for the Nexmo channel.
     *
     * @return string
     */
    public function routeNotificationForNexmo()
    {
        return $this->primaryAddress->phone;
    }

    /**
     * Get all of the country for the country.
     */
    public function country()
    {
        return $this->hasManyThrough(Country::class, Address::class, 'addressable_id', 'country_name');
    }

    /**
     * Get the user wishlists.
     */
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Get the user orders.
     */
    public function orders()
    {
        return $this->hasMany(Order::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the user downloadables.
     */
    public function downloadables()
    {
        return $this->hasMany(Order::class)->where('is_digital', 1)
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get credit rewards associated with the customer.
     */
    public function creditRewards()
    {
        return $this->hasMany(\Incevio\Package\Wallet\Models\CreditReward::class);
    }

    /**
     * Get the user latest_orders.
     */
    public function latest_orders()
    {
        return $this->orders()->orderBy('created_at', 'desc')->limit(5);
    }

    /**
     * Get the user carts.
     */
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Get the messages for the customer.
     */
    public function messages()
    {
        return $this->hasMany(Message::class)->notArchived()
            ->orderBy('updated_at', 'desc')->orderBy('customer_status');
    }

    /**
     * Get the coupons for the customer.
     */
    public function coupons()
    {
        return $this->belongsToMany(Coupon::class)->active()
            ->orderBy('ending_time', 'desc')->withTimestamps();
    }

    public function disputes()
    {
        return $this->hasMany(Dispute::class);
    }

    public function refunds()
    {
        return $this->hasManyThrough(Refund::class, Order::class);
    }

    /**
     * Get the user gift_cards.
     */
    public function gift_cards()
    {
        return $this->hasMany(GiftCard::class);
    }

    /**
     * get the list of events of the customer.
     */
    public function events()
    {
        return $this->belongsToMany(\Incevio\Package\Eventy\Models\Event::class);
    }

    /**
     * Get the buyer group of the customer.
     */
    public function buyerGroup()
    {
        if (is_incevio_package_loaded('buyerGroup')) {
            return $this->belongsTo(\Incevio\Package\BuyerGroup\Models\BuyerGroup::class);
        }

        return null;
    }

    /**
     * Get the buyer group of the customer.
     */
    public function appliedBuyerGroup()
    {
        if (is_incevio_package_loaded('buyerGroup')) {
            return $this->belongsTo(\Incevio\Package\BuyerGroup\Models\BuyerGroup::class, 'buyer_group_requested_id');
        }

        return null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     * Get the bids from the customer
     */
    public function bids()
    {
        return $this->belongsToMany(Inventory::class, 'auction_bids')
            ->withPivot(['amount_in_system_currency', 'amount_in_bid_currency'])
            ->withTimestamps();
    }

    /**
     * Check if the customer has billing token
     *
     * @return bool
     */
    public function hasBillingToken()
    {
        return $this->hasStripeId();
    }

    /**
     * Check if the user is Verified
     *
     * @return bool
     */
    public function isVerified()
    {
        return $this->verification_token == null;
    }

    /**
     * Check if user is approved
     *
     * @return bool
     */
    public function isApproved()
    {
        return (bool) $this->approval_status;
    }

    /**
     * Setters
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::needsRehash($password) ? Hash::make($password) : $password;
    }

    public function setAcceptsMarketingAttribute($value)
    {
        $this->attributes['accepts_marketing'] = $value ? 1 : null;
    }

    public function setExtraInfoAttribute($value)
    {
        $this->attributes['extra_info'] = serialize($value);
    }

    public function getExtraInfoAttribute($value)
    {
        return unserialize($value);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomerResetPasswordNotification($token));
    }

    /**
     * Scope a query to only include active records.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }
}
