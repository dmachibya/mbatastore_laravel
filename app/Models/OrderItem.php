<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderItem extends Pivot
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'order_items';

    /**
     * Get the Inventory associated with the model.
     */
    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }

    /**
     * Get the order associated with the model.
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
