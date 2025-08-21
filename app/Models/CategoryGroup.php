<?php

namespace App\Models;

use App\Common\CascadeSoftDeletes;
use App\Common\Imageable;
use App\Common\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Builder;

class CategoryGroup extends BaseModel
{
    use HasFactory, SoftDeletes, CascadeSoftDeletes, Imageable, Translatable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'category_groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description', 'slug', 'icon', 'order', 'active', 'meta_title', 'meta_description'];

    /**
     * Cascade Soft Deletes Relationships
     *
     * @var array
     */
    protected $cascadeDeletes = ['subGroups'];

    /**
     * The boot method for the CategoryGroup model.
     *
     * This method is called when the CategoryGroup model is being booted.
     * It adds a global scope to the model to include translations based on the current locale.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('withTranslations', function (Builder $builder) {
            $builder->with(['translations' => function ($query) {
                $query->where('lang', app()->getLocale())->whereNotNull('translation');
            }]);
        });
    }

    /**
     * Get the subGroups associated with the CategoryGroup.
     */
    public function subGroups()
    {
        return $this->hasMany(CategorySubGroup::class, 'category_group_id')->orderBy('order', 'asc');
    }

    /**
     * Get the categories associated with the CategoryGroup.
     */
    public function categories()
    {
        return $this->hasManyThrough(
            Category::class,
            CategorySubGroup::class,
            'category_group_id', // Foreign key on CategorySubGroup table...
            'category_sub_group_id', // Foreign key on Category table...
            'id', // Local key on CategoryGroup table...
            'id' // Local key on CategorySubGroup table...
        );
    }

    public function translations()
    {
        return $this->hasMany(CategoryGroupTranslation::class);
    }

    /**
     * Setters
     */
    public function setOrderAttribute($value)
    {
        $this->attributes['order'] = $value ?? 100;
    }

    public function getNameAttribute($value)
    {
        return $this->translateAttribute('name') ?? $value;
    }

    public function getDescriptionAttribute($value)
    {
        return $this->translateAttribute('description') ?? $value;
    }

    protected function getTranslationDisabledRoutes()
    {
        return ['admin.catalog.categoryGroup.edit'];
    }
}
