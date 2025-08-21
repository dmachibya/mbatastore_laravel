<?php

namespace App\Models;

abstract class TranslationModel extends BaseModel
{
    /**
     * Set the translation attribute as serialized object.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setTranslationAttribute($value)
    {
        $this->attributes['translation'] = base64_encode(serialize($value));
    }

    /**
     * Get the translation attribute as a php object.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function getTranslationAttribute($value)
    {
        return unserialize(base64_decode($value));
    }
}
