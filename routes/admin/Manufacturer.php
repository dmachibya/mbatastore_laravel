<?php

use App\Http\Controllers\Admin\ManufacturerController;
use App\Http\Controllers\Admin\ManufacturerTranslationController;
use Illuminate\Support\Facades\Route;

Route::delete('manufacturer/{manufacturer}/trash', [ManufacturerController::class, 'trash'])->name('manufacturer.trash');

Route::post('manufacturer/massTrash', [ManufacturerController::class, 'massTrash'])->name('manufacturer.massTrash')->middleware('demoCheck');

Route::post('manufacturer/massDestroy', [ManufacturerController::class, 'massDestroy'])->name('manufacturer.massDestroy')->middleware('demoCheck');

Route::delete('manufacturer/emptyTrash', [ManufacturerController::class, 'emptyTrash'])->name('manufacturer.emptyTrash');

Route::get('manufacturer/{manufacturer}/restore', [ManufacturerController::class, 'restore'])->name('manufacturer.restore');

Route::resource('manufacturer', ManufacturerController::class);

// Translation Routes
Route::get('manufacturer/translate/{manufacturer}/{language}', [
    ManufacturerTranslationController::class, 'showTranslationForm'
])->name('manufacturer.translate.form');

Route::post('manufacturer/translate/{manufacturer}', [
    ManufacturerTranslationController::class, 'storeTranslation'
])->name('manufacturer.translate.store');

Route::get('manufacturer/translate/bulk', [
    ManufacturerTranslationController::class, 'showBulkUploadForm'
])->name('manufacturer.translate.bulk')->middleware('ajax');

Route::post('manufacturer/translate/bulk', [
    ManufacturerTranslationController::class, 'storeBulkTranslation'
])->name('manufacturer.translate.bulk.store');

Route::post('manufacturer/translation/upload', [
    ManufacturerTranslationController::class, 'uploadBulkTranslation'
])->name('manufacturer.translate.bulk.upload');

Route::post('manufacturer/translation/import', [
    ManufacturerTranslationController::class, 'importBulkTranslation'
])->name('manufacturer.translate.bulk.import');

Route::get('manufacturer/translation/download/template', [
    ManufacturerTranslationController::class, 'downloadTemplate'
])->name('manufacturer.translate.download.template');

Route::get('manufacturer/translation/download/failedRows', [
    ManufacturerTranslationController::class, 'downloadFailedRows'
])->name('manufacturer.translate.download.failedRows');