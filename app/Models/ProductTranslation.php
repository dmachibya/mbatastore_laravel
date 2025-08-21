<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductTranslation extends TranslationModel
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'translation_products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'lang',
        'translation',
    ];

    /**
     * Get the product that owns the translation.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
