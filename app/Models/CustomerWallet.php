<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

if (trait_exists(\Incevio\Package\Wallet\Traits\HasWallet::class)) {
    abstract class CustomerWallet extends Authenticatable
    {
        use \Incevio\Package\Wallet\Traits\HasWallet;
    }
} else {
    abstract class CustomerWallet extends Authenticatable
    {
    }
}
