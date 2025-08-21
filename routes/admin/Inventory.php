<?php

use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\InventoryUploadController;
use App\Http\Controllers\Admin\InventoryBulkUpdateController;
use App\Http\Controllers\Admin\InventoryTranslationController;
use Illuminate\Support\Facades\Route;

// Bulk upload routes
Route::get('inventory/upload/downloadTemplate', [
  InventoryUploadController::class, 'downloadTemplate'
])->name('inventory.downloadTemplate');

Route::get('inventory/upload', [
  InventoryUploadController::class, 'showForm'
])->name('inventory.bulk');

Route::post('inventory/upload', [
  InventoryUploadController::class, 'upload'
])->name('inventory.upload');

Route::post('inventory/import', [
  InventoryUploadController::class, 'import'
])->name('inventory.import');

Route::post('inventory/downloadFailedRows', [
  InventoryUploadController::class, 'downloadFailedRows'
])->name('inventory.downloadFailedRows');

// Bulk update routes
Route::get('inventory/bulkUpdate', [
  InventoryBulkUpdateController::class, 'showForm'
])->name('inventory.bulkUpdate');

Route::post('inventory/bulkUpdate', [ 
  InventoryBulkUpdateController::class, 'update'
])->name('inventory.bulkUpdate'); 

Route::get('inventory/bulkUpdate/downloadTemplate', [
  InventoryBulkUpdateController::class, 'downloadTemplate'
])->name('inventory.bulkUpdate.downloadTemplate');

Route::get('inventory/bulkUpdate/downloadFailedRows', [
  InventoryBulkUpdateController::class, 'downloadFailedRows'
])->name('inventory.bulkUpdate.downloadFailedRows');

Route::post('inventory/bulkUpdate/import', [
  InventoryBulkUpdateController::class, 'import'
])->name('inventory.bulkUpdate.import');

// Mass Actions
Route::post('inventory/massTrash', [
  InventoryController::class, 'massTrash'
])->name('inventory.massTrash')->middleware('demoCheck');

Route::post('inventory/massDestroy', [
  InventoryController::class, 'massDestroy'
])->name('inventory.massDestroy')->middleware('demoCheck');

Route::delete('inventory/emptyTrash', [
  InventoryController::class, 'emptyTrash'
])->name('inventory.emptyTrash');

// inventories
Route::delete('inventory/{inventory}/trash', [
  InventoryController::class, 'trash'
])->name('inventory.trash'); // inventory move to trash

Route::get('inventory/{inventory}/restore', [
  InventoryController::class, 'restore'
])->name('inventory.restore');

Route::get('inventory/setVariant/{product}', [
  InventoryController::class, 'setVariant'
])->name('inventory.setVariant');

Route::get('inventory/add/{product}', [
  InventoryController::class, 'add'
])->name('inventory.add');

Route::get('inventory/addWithVariant/{product}', [
  InventoryController::class, 'addWithVariant'
])->name('inventory.addWithVariant');

Route::post('inventory/storeWithVariant', [
  InventoryController::class, 'storeWithVariant'
])->name('inventory.storeWithVariant');

Route::post('inventory/store', [
  InventoryController::class, 'store'
])->name('inventory.store')->middleware('ajax');

Route::post('inventory/{inventory}/update', [
  InventoryController::class, 'update'
])->name('inventory.update')->middleware('ajax');

Route::get('inventory/{inventory}/editQtt', [
  InventoryController::class, 'editQtt'
])->name('inventory.editQtt');

Route::put('inventory/{inventory}/updateQtt', [
  InventoryController::class, 'updateQtt'
])->name('inventory.updateQtt');

Route::get('inventory/{status}/getInventory/{type?}', [
  InventoryController::class, 'getInventory'
])->name('inventory.getMore')->middleware('ajax');

Route::get('inventory/{type?}', [
  InventoryController::class, 'index'
])->name('inventory.index');

Route::get('inventory/{inventory}/addVariant', [
  InventoryController::class, 'singleVariantForm'
])->name('inventory.addVariant');

Route::post('inventory/{inventory}/saveVariant', [
  InventoryController::class, 'saveSingleVariant'
])->name('inventory.saveVariant');

Route::get('inventory/getCombinations', [
  InventoryController::class, 'getCombinations'
])->name('inventory.getCombinations');

Route::get('inventory/{inventory}/show', [
  InventoryController::class, 'show'
])->name('inventory.show');

// Translation routes
Route::get('inventory/translate/{inventory}/{language}', [
  InventoryTranslationController::class, 'showTranslationForm'
])->name('inventory.translation.form');

Route::post('inventory/translate/{inventory}', [
  InventoryTranslationController::class, 'storeTranslation'
])->name('inventory.translation.store');

Route::get('inventory/translate/bulk', [
  InventoryTranslationController::class, 'showBulkUploadForm'
])->name('inventory.translate.bulk')->middleware('ajax');

Route::post('inventory/translate/bulk/upload', [
  InventoryTranslationController::class, 'uploadBulkTranslation'
])->name('inventory.translate.bulk.upload');

Route::post('inventory/translate/bulk/import', [
  InventoryTranslationController::class, 'importBulkTranslation'
])->name('inventory.translate.bulk.import');

Route::get('inventory/translation/download/failedRows', [
  InventoryTranslationController::class, 'downloadFailedRows'
])->name('inventory.translate.download.failedRows');

Route::get('inventory/translation/downloadTemplate', [
  InventoryTranslationController::class, 'downloadTemplate'
])->name('inventory.translate.download.template');

// Resource routes
Route::resource('inventory', InventoryController::class)->except('create', 'store', 'show', 'update', 'index');
