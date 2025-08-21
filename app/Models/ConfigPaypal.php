<?php

namespace App\Models;



class ConfigPaypal extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $table = 'config_paypal_express';

    /**
     * The database primary key used by the model.
     *
     * @var string
     */
    protected $primaryKey = 'shop_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'shop_id',
        'client_id',
        'secret',
        'sandbox',
    ];

}
