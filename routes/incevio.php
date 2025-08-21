<?php

use Carbon\Carbon;
use App\Models\Shop;
use App\Models\Country;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\Inventory;
use Illuminate\Support\Str;
use App\Models\Manufacturer;
use App\Models\CategoryGroup;
use App\Models\AttributeValue;
use App\Models\CategorySubGroup;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Incevio;
use Illuminate\Support\Facades\Artisan;

Route::middleware('xssSanitizer')->prefix('incevio')->group(function () {
    Route::get('dart', function () {
        dd('do working');
    });

    // Check different type system information
    Route::get('check/{option?}', [Incevio::class, 'check'])->name('incevio.check');

    // New version upgrade
    Route::get('upgrade/{option?}', [Incevio::class, 'upgrade'])->name('incevio.upgrade');

    // Run Artisan command
    Route::get('command/{option?}', [Incevio::class, 'command'])->name('incevio.command');

    // Clear config. cache etc
    Route::get('clear/{all?}', [Incevio::class, 'clear'])->name('incevio.clear');

    // Clear scout. cache etc
    // Route::get('scout/{all?}', [Incevio::class, 'scout'])->name('incevio.scout');
});
