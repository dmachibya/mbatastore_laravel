<?php

use App\Http\Controllers\Admin\FlashdealController;
use Illuminate\Support\Facades\Route;

Route::get('flashdeal', [FlashdealController::class, 'index'])->name('flashdeal');
Route::post('flashdeal/create', [FlashdealController::class, 'create'])->name('flashdeal.create');
