<?php

namespace App\Repositories\Warehouse;

interface WarehouseRepository
{
    public function saveAddress(array $address, $warehouse);
}
