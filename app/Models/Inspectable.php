<?php

namespace App\Models;

if (trait_exists(\Incevio\Package\Inspector\Traits\HasInspector::class)) {
    abstract class Inspectable extends BaseModel
    {
        use \Incevio\Package\Inspector\Traits\HasInspector;
    }
} else {
    abstract class Inspectable extends BaseModel
    {
    }
}
