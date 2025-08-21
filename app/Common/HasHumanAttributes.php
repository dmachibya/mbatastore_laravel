<?php

namespace App\Common;

use Illuminate\Support\Arr;

trait HasHumanAttributes
{
    /**
     * Get name the user.
     *
     * @return string
     */
    public function getName()
    {
        return $this->nice_name ?? $this->full_name;
    }

    /**
     * This will returns full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->first_name ? $this->first_name . ' ' . $this->last_name : $this->name;
    }

    /**
     * Get first name the user.
     *
     * @return string
     */
    public function getFirstNameAttribute()
    {
        return explode(' ', $this->name)[0];
    }

    /**
     * Get last name the user.
     *
     * @return string
     */
    public function getLastNameAttribute()
    {
        $arr = explode(' ', $this->name);

        return count($arr) > 1 ? Arr::last($arr) : '';
    }

    /**
     * Get dob for the user.
     *
     * @return string
     */
    public function getDobAttribute($dob)
    {
        if ($dob) {
            return date('Y-m-d', strtotime($dob));
        }
    }
}
