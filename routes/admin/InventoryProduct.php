<?php

use App\Http\Controllers\Admin\ProductController;
use Illuminate\Support\Facades\Route;

// Product routes
Route::post('product/massTrash', [
  ProductController::class, 'massTrash'
])->name('product.massTrash');

Route::post('product/massDestroy', [
  ProductController::class, 'massDestroy'
])->name('product.massDestroy');

Route::delete('product/emptyTrash', [
  ProductController::class, 'emptyTrash'
])->name('product.emptyTrash');

Route::delete('product/{product}/trash', [
  ProductController::class, 'trash'
])->name('product.trash'); // product move to trash

Route::get('product/{product}/restore', [
  ProductController::class, 'restore'
])->name('product.restore');

Route::post('product/store', [
  ProductController::class, 'store'
])->name('product.store')->middleware('ajax');

Route::post('product/{product}/update', [
  ProductController::class, 'update'
])->name('product.update')->middleware('ajax');

Route::get('product/getProducts', [
  ProductController::class, 'getProducts'
])->name('product.getMore');

Route::get('product/get-attributes-by-categories', [
  ProductController::class, 'getAttributesByCategories'
])->name('product.getAttributesByCategories');

Route::get('product/{product}/addVariant', [
  ProductController::class, 'singleVariantForm'
])->name('product.addVariant');

Route::post('product/{product}/saveVariant', [
  ProductController::class, 'saveSingleVariant'
])->name('product.saveVariant');

Route::get('product/getCombinations', [
  ProductController::class, 'getCombinations'
])->name('product.getCombinations');

Route::get('product/digital', [
  ProductController::class, 'index'
])->name('product.digital.index');

Route::get('product/physical', [
  ProductController::class, 'index'
])->name('product.physical.index');

Route::get('product/auction', [
  ProductController::class, 'index'
])->name('product.auction.index');

Route::resource('product', ProductController::class)->except('store', 'update');