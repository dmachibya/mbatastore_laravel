<?php

use App\Http\Controllers\Admin\CategorySubGroupController;
use App\Http\Controllers\Admin\CategorySubGroupTranslationController;
use Illuminate\Support\Facades\Route;

Route::delete('categorySubGroup/{categorySubGroup}/trash', [CategorySubGroupController::class, 'trash'])
  ->name('categorySubGroup.trash');

Route::post('categorySubGroup/massTrash', [CategorySubGroupController::class, 'massTrash'])
  ->name('categorySubGroup.massTrash');

Route::post('categorySubGroup/massDestroy', [CategorySubGroupController::class, 'massDestroy'])
  ->name('categorySubGroup.massDestroy');

Route::delete('categorySubGroup/emptyTrash', [CategorySubGroupController::class, 'emptyTrash'])
  ->name('categorySubGroup.emptyTrash');

Route::get('categorySubGroup/{categorySubGroup}/restore', [CategorySubGroupController::class, 'restore'])
  ->name('categorySubGroup.restore');

Route::get('categorySubGroup/{categorySubGroup}/parentCategories', [CategorySubGroupController::class, 'showParentCategories'])
  ->name('categorySubGroup.showParentCategories');

Route::resource('categorySubGroup', CategorySubGroupController::class)->except('show');

// Translation routes
Route::get('categorySubGroup/translate/{categorySubGroup}/{language}', [
  CategorySubGroupTranslationController::class, 'showTranslationForm'
])->name('categorySubGroup.translate.form');

Route::post('categorySubGroup/translate/{categorySubGroup}', [
  CategorySubGroupTranslationController::class, 'storeTranslation'
])->name('categorySubGroup.translate.store');

Route::get('categorySubGroup/translate/bulk', [
  CategorySubGroupTranslationController::class, 'showBulkUploadForm'
])->name('categorySubGroup.translate.bulk')->middleware('ajax');

Route::post('categorySubGroup/translate/bulk/upload', [
  CategorySubGroupTranslationController::class, 'uploadBulkTranslation'
])->name('categorySubGroup.translate.bulk.upload');

Route::post('categorySubGroup/translate/bulk/import', [
  CategorySubGroupTranslationController::class, 'importBulkTranslation'
])->name('categorySubGroup.translate.bulk.import');

Route::get('categorySubGroup/translation/download/failedRows', [
  CategorySubGroupTranslationController::class, 'downloadFailedRows'
])->name('categorySubGroup.translate.download.failedRows');

Route::get('categorySubGroup/translation/downloadTemplate', [
  CategorySubGroupTranslationController::class, 'downloadTemplate'
])->name('categorySubGroup.translate.download.template');
