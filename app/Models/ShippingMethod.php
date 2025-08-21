<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;

class ShippingMethod extends BaseModel
{
    const TYPE_MANUAL = 1;
    const TYPE_ONLINE = 2;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'shipping_methods';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'enabled' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'company_name',
        'type',
        'code',
        'website',
        'help_doc_url',
        'admin_help_doc_link',
        'terms_conditions_link',
        'description',
        'instructions',
        'admin_description',
        'enabled',
        'order',
    ];

    /**
     * Get the shops for the inventory.
     */
    public function shops()
    {
        return $this->belongsToMany(Shop::class, 'shop_shipping_methods', 'shipping_method_id', 'shop_id')
            ->withTimestamps();
    }

    /**
     * Scope a query to only include active records.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('enabled', 1);
    }

    /**
     * Scope a query to include online shipping methods only.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOnline($query)
    {
        return $query->where('type', '!=', static::TYPE_MANUAL);
    }

    /**
     * Scope a query to include offline shipping methods only.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOffline($query)
    {
        return $query->where('type', static::TYPE_MANUAL);
    }

    /**
     * Get the user's first name.
     *
     * @param  string  $value
     * @return string
     */
    public function getNameAttribute($value)
    {
        if ($this->code == 'zcart-wallet') {
            return get_platform_title() . ' ' . $value;
        }

        return $value;
    }
}
