<?php

use App\Http\Controllers\Admin\CategoryGroupController;
use App\Http\Controllers\Admin\CategoryGroupTranslationController;
use Illuminate\Support\Facades\Route;

Route::delete('categoryGroup/{categoryGrp}/trash', [CategoryGroupController::class, 'trash'])
  ->name('categoryGroup.trash');

Route::get('categoryGroup/{categoryGrp}/restore', [CategoryGroupController::class, 'restore'])
  ->name('categoryGroup.restore');

Route::post('categoryGroup/massTrash', [CategoryGroupController::class, 'massTrash'])
  ->name('categoryGroup.massTrash');

Route::post('categoryGroup/massDestroy', [CategoryGroupController::class, 'massDestroy'])
  ->name('categoryGroup.massDestroy');

Route::delete('categoryGroup/emptyTrash', [CategoryGroupController::class, 'emptyTrash'])
  ->name('categoryGroup.emptyTrash');

Route::get('categoryGroup/{categoryGrp}/subGroups', [CategoryGroupController::class, 'showChildSubGroups'])
  ->name('categoryGroup.subGroups');

Route::resource('categoryGroup', CategoryGroupController::class)->except('show');

// Translation routes
Route::get('categoryGroup/translate/{category_group}/{language}', [
  CategoryGroupTranslationController::class, 'showTranslationForm'
])->name('categoryGroup.translate.form');

Route::post('categoryGroup/translate/{category_group}', [
  CategoryGroupTranslationController::class, 'storeTranslation'
])->name('categoryGroup.translate.store');

Route::get('categoryGroup/translate/bulk', [
  CategoryGroupTranslationController::class, 'showBulkUploadForm'
])->name('categoryGroup.translate.bulk')->middleware('ajax');

Route::post('categoryGroup/translate/bulk/upload', [
  CategoryGroupTranslationController::class, 'uploadBulkTranslation'
])->name('categoryGroup.translate.bulk.upload');

Route::post('categoryGroup/translate/bulk/import', [
  CategoryGroupTranslationController::class, 'importBulkTranslation'
])->name('categoryGroup.translate.bulk.import');

Route::get('categoryGroup/translation/download/failedRows', [
  CategoryGroupTranslationController::class, 'downloadFailedRows'
])->name('categoryGroup.translate.download.failedRows');

Route::get('categoryGroup/translation/downloadTemplate', [
  CategoryGroupTranslationController::class, 'downloadTemplate'
])->name('categoryGroup.translate.download.template');

