<?php

namespace App\Models;

class CategorySubGroupTranslation extends TranslationModel
{
    protected $table = 'translation_category_sub_groups';

    protected $fillable = [
        'category_sub_group_id',
        'lang',
        'translation',
    ];

    public function subGroup()
    {
        return $this->belongsTo(CategorySubGroup::class);
    }
}