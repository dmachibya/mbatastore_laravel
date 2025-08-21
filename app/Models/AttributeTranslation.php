<?php

namespace App\Models;

class AttributeTranslation extends TranslationModel
{
    protected $table = 'translation_attributes';

    protected $fillable = [
        'attribute_id',
        'lang',
        'translation',
    ];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }
}