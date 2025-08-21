<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryTranslation extends TranslationModel
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'translation_inventories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'inventory_id',
        'lang',
        'translation',
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}