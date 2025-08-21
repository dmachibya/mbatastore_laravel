<?php

use App\Http\Controllers\Admin\CatalogProductController;
use App\Http\Controllers\Admin\ProductUploadController;
use App\Http\Controllers\Admin\CatalogProductTranslationController;
use Illuminate\Support\Facades\Route;

// Bulk upload routes
Route::get('product/upload/downloadCategorySlugs', [ProductUploadController::class, 'downloadCategorySlugs'])->name('product.downloadCategorySlugs');

Route::get('product/upload/downloadTemplate', [ProductUploadController::class, 'downloadTemplate'])->name('product.downloadTemplate');

Route::get('product/upload', [ProductUploadController::class, 'showForm'])->name('product.bulk');

Route::post('product/upload', [ProductUploadController::class, 'upload'])->name('product.upload');

Route::post('product/import', [ProductUploadController::class, 'import'])->name('product.import');

Route::post('product/downloadFailedRows', [ProductUploadController::class, 'downloadFailedRows'])->name('product.downloadFailedRows');

// Catalog Product routes

Route::post('product/massTrash', [CatalogProductController::class, 'massTrash'])->name('product.massTrash');

Route::post('product/massDestroy', [CatalogProductController::class, 'massDestroy'])->name('product.massDestroy');

Route::delete('product/emptyTrash', [CatalogProductController::class, 'emptyTrash'])->name('product.emptyTrash');

Route::delete('product/{product}/trash', [CatalogProductController::class, 'trash'])->name('product.trash'); // product move to trash

Route::get('product/{product}/restore', [CatalogProductController::class, 'restore'])->name('product.restore');

Route::post('product/store', [CatalogProductController::class, 'store'])->name('product.store')->middleware('ajax');

Route::post('product/{product}/update', [CatalogProductController::class, 'update'])->name('product.update')->middleware('ajax');

Route::get('product/getProducts', [CatalogProductController::class, 'getProducts'])->name('product.getMore');

Route::resource('product', CatalogProductController::class)->except('store', 'update');

// Product translation routes

Route::get('product/translate/{product}/{language}', [
    CatalogProductTranslationController::class, 'showTranslationForm'
])->name('product.translate.form');

Route::post('product/translate/{product}', [
    CatalogProductTranslationController::class, 'storeTranslation'
])->name('product.translate.store');


Route::get('product/translate/bulk', [
   CatalogProductTranslationController::class, 'showBulkUploadForm'
])->name('product.translate.bulk')->middleware('ajax');
  
Route::post('product/translation/bulk/upload', [
    CatalogProductTranslationController::class, 'uploadBulkTranslation'
])->name('product.translate.bulk.upload');

Route::post('product/translation/bulk/import', [
    CatalogProductTranslationController::class, 'importBulkTranslation'
])->name('product.translate.bulk.import');

Route::get('product/translation/download/failedRows', [
    CatalogProductTranslationController::class, 'downloadFailedRows'
])->name('product.translate.download.failedRows');

Route::get('product/translation/download/template', [
    CatalogProductTranslationController::class, 'downloadTemplate'
])->name('product.translate.download.template');
  
