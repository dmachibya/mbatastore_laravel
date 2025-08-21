<?php

namespace App\Models;

use App\Common\Imageable;
use App\Common\Addressable;
use App\Common\SystemUsers;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class System extends BaseModel
{
    use SystemUsers, Notifiable, Addressable, Imageable, LogsActivity;

    /**
     * The zCart version.
     *
     * @var string
     */
    const VERSION = '2.18.0'; // The current version

    /**
     * API version compatibilities
     *
     * @var array
     */
    public static $api_compatibility = [
        'customer' => '2.3.*',
        'vendor' => '1.0.*',
        'delivery' => '1.0.*',
    ];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'systems';

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
    protected static $logName = 'system';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'install_verion',
        'maintenance_mode',
        'name',
        'legal_name',
        'slogan',
        'email',
        'worldwide_business_area',
        'timezone_id',
        'currency_id',
        'update_currency_rate_at',
        'currency_api',
        'currency_api_key',
        'default_language',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'worldwide_business_area' => 'boolean',
        'maintenance_mode' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        // Observer for Run currency rate update command if currency is changed
        if (is_incevio_package_loaded('dynamic-currency')) {
            static::updated(function ($model) {
                if ($model->isDirty('currency_id')) {
                    Artisan::call('incevio:currency-rate-update');
                }
            });
        }
    }

    /**
     * Route notifications for the mail channel.
     *
     * @return string
     */
    public function routeNotificationForMail()
    {
        return $this->support_email ? $this->support_email : $this->email;
    }

    /**
     * Route notifications for the Nexmo channel.
     *
     * @return string
     */
    public function routeNotificationForNexmo()
    {
        return $this->support_phone ? $this->support_phone : $this->primaryAddress->phone;
    }

    /**
     * Get the currency associated with the blog post.
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get the timezone associated with the blog post.
     */
    public function timezone()
    {
        return $this->belongsTo(Timezone::class);
    }

    /**
     * Getters
     */
    public function getBusinessAreaAttribute($value)
    {
        return $value ? trans('app.worldwide') : trans('app.active_business_area');
    }

    /**
     * Check if the system is down or live.
     *
     * @return bool
     */
    public function isDown()
    {
        return $this->maintenance_mode;
    }

    public function getActivitylogOptions(): LogOptions
    {
        $logOptions = LogOptions::defaults();

        return $logOptions->logAll();
    }
}
