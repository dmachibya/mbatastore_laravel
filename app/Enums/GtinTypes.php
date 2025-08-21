<?php

namespace App\Enums;

use Illuminate\Support\Collection;

class GtinTypes
{
    const UPC = 'UPC';
    const EAN = 'EAN';
    const JAN = 'JAN';
    const ISBN = 'ISBN';
    const ITF = 'ITF';
    const NPN = 'NPN';
    const DIN = 'DIN';

    /**
     * Get a single value of given key
     *
     * @param string $arguments
     * @return string|null
     */
    public static function getValue($arguments)
    {
        return isset($arguments) ? constant("self::$arguments") : null;
    }

    /**
     * Get the list view of all values of the enum as collection
     *
     * @return Collection the collection of GTIN types
     */
    public static function list(): Collection
    {
        return collect([
            self::UPC => self::UPC,
            self::EAN => self::EAN,
            self::JAN => self::JAN,
            self::ISBN => self::ISBN,
            self::ITF => self::ITF,
            self::NPN => self::NPN,
            self::DIN => self::DIN,
        ]);
    }
}
