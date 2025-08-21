<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CategoryTranslationController;
use Illuminate\Support\Facades\Route;

Route::delete('category/{category}/trash', [CategoryController::class, 'trash'])->name('category.trash'); // category post move to trash

Route::post('category/massTrash', [CategoryController::class, 'massTrash'])->name('category.massTrash');

Route::post('category/massDestroy', [CategoryController::class, 'massDestroy'])->name('category.massDestroy');

Route::delete('category/emptyTrash', [CategoryController::class, 'emptyTrash'])->name('category.emptyTrash');

Route::get('category/{category}/restore', [CategoryController::class, 'restore'])->name('category.restore');

Route::get('category/getMoreCategories', [CategoryController::class, 'getCategories'])
  ->name('category.getMore')->middleware('ajax');

Route::resource('category', CategoryController::class)->except('show');

// Translation routes
Route::get('category/translate/{category}/{language}', [
  CategoryTranslationController::class, 'showTranslationForm'
])->name('category.translate.form');

Route::post('category/translate/{category}', [
  CategoryTranslationController::class, 'storeTranslation'
])->name('category.translate.store');

Route::get('category/translate/bulk', [
  CategoryTranslationController::class, 'showBulkUploadForm'
])->name('category.translate.bulk')->middleware('ajax');

Route::post('category/translate/bulk/upload', [
  CategoryTranslationController::class, 'uploadBulkTranslation'
])->name('category.translate.bulk.upload');

Route::post('category/translate/bulk/import', [
  CategoryTranslationController::class, 'importBulkTranslation'
])->name('category.translate.bulk.import');

Route::get('category/translation/download/failedRows', [
  CategoryTranslationController::class, 'downloadFailedRows'
])->name('category.translate.download.failedRows');

Route::get('category/translation/downloadTemplate', [
  CategoryTranslationController::class, 'downloadTemplate'
])->name('category.translate.download.template');
