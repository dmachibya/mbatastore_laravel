<?php

use App\Http\Controllers\Admin\ThemeController;
use Illuminate\Support\Facades\Route;

// Theme
Route::middleware(['userType:admin'])->group(function () {
  Route::get('/theme', [ThemeController::class, 'all'])->name('theme.index');

  Route::get('theme/{theme}/initiate', [ThemeController::class, 'initiate'])->name('theme.initiate');

  Route::put('/theme/activate/{theme}/{type?}', [ThemeController::class, 'activate'])
    ->name('theme.activate');
});
