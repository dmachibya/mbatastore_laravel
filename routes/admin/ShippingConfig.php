<?php

use App\Http\Controllers\Admin\ShippingMethodController;
use Illuminate\Support\Facades\Route;

// General
Route::get('shippingMethod', [
    ShippingMethodController::class, 'index'
])->name('config.shippingMethod.index');

Route::get('shippingMethod/{shippingMethod}/activate', [
    ShippingMethodController::class, 'activate'
])->name('shippingMethod.activate');

Route::get('shippingMethod/{shippingMethod}/deactivate', [
    ShippingMethodController::class, 'deactivate'
])->name('shippingMethod.deactivate');
