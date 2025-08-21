<?php

namespace App\Common;

use Illuminate\Support\Facades\Route;

/**
 * Attach this Trait to a model to have the ability of translating the model
 */
trait Translatable
{

    private $translationExists = [];

    public function translations() {
        // Get translation class name
        $currentClassName = class_basename($this);
        $translationClassName = "{$currentClassName}Translation";

        if (class_exists($translationClassName)) {
            return $this->hasMany($this->translationClassName);
        }
        
        return null;
    }

    /**
     * Check if the model has a translation for the specified language.
     *
     * @param string|null $lang The language code (e.g., 'en', 'fr'). If null, the current application locale will be used.
     * @return bool
     */
    public function hasTranslation($lang = null)
    {
        $lang = $lang ?? app()->getLocale();

        if (!array_key_exists($lang, $this->translationExists)) {
            $this->translationExists[$lang] = $this->translations()->where('lang', $lang)->exists();
        }

        return $this->translationExists[$lang];
    }

    /**
     * Translate given attributes value from translation table for this model.
     *
     * @param string $attribute - attribute name to translate
     *
     * @return string - translated value of the attribute
     */
    public function translateAttribute(string $attribute)
    {
        if ($this->routeHasTranslationDisabled()) {
            return null;
        }

        $model_translation = $this->translations->first();

        if (!$model_translation || !isset($model_translation->translation[$attribute])) {
            return null;
        }

        return $model_translation->translation[$attribute];
    }

    /**
     * Check if the current route has translation Disabled for this model
     *
     * @return bool
     */
    private function routeHasTranslationDisabled() : bool
    {
        return in_array(Route::getCurrentRoute(), $this->getTranslationDisabledRoutes());
    }

    /**
     * Return an array of route names for which the translation will be disabled.
     *
     * @return array
     */
    protected function getTranslationDisabledRoutes() : array
    {
        return [];
    }
}