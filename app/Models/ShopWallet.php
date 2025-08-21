<?php

namespace App\Models;

if (trait_exists(\Incevio\Package\Wallet\Traits\HasWallet::class)) {
    abstract class ShopWallet extends BaseModel
    {
        use \Incevio\Package\Wallet\Traits\HasWallet;
    }
} else {
    abstract class ShopWallet extends BaseModel
    {
    }
}
