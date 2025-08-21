<?php

namespace App\Models;

class ManufacturerTranslation extends TranslationModel
{
    protected $table = 'translation_manufacturers';

    protected $fillable = [
        'manufacturer_id',
        'lang',
        'translation'
    ];

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }
}