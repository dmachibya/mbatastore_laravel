<?php

use App\Http\Controllers\Admin\ShopController;
use App\Http\Controllers\Admin\ShopTranslationController;
use App\Http\Controllers\Admin\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::post('shop/massTrash', [ShopController::class, 'massTrash'])->name('shop.massTrash')->middleware('demoCheck');

Route::post('shop/massDestroy', [ShopController::class, 'massDestroy'])->name('shop.massDestroy')->middleware('demoCheck');

Route::delete('shop/emptyTrash', [ShopController::class, 'emptyTrash'])->name('shop.emptyTrash');

Route::get('shop/verifications', [ShopController::class, 'verifications'])->name('shop.verifications');

Route::get('shop/{shop}/verify', [ShopController::class, 'showVerificationForm'])->name('shop.verify');

Route::get('subscription/{shop}/editTrial', [SubscriptionController::class, 'editTrial'])->name('subscription.editTrial');

Route::put('subscription/{shop}/updateTrial', [SubscriptionController::class, 'updateTrial'])->name('subscription.updateTrial');

Route::put('shop/{shop}/toggle', [ShopController::class, 'toggleStatus'])->name('shop.toggle')->middleware('ajax');

Route::get('shop/{shop}/staffs', [ShopController::class, 'staffs'])->name('shop.staffs');

Route::delete('shop/{shop}/trash', [ShopController::class, 'trash'])->name('shop.trash'); // shop move to trash

Route::get('shop/{shop}/restore', [ShopController::class, 'restore'])->name('shop.restore');

Route::resource('shop', ShopController::class)->except('create', 'store');

// Translation routes
Route::get('shop/translate/{shop}/{language}', [
    ShopTranslationController::class, 'showTranslationForm'
])->name('shop.translate.form');

Route::post('shop/translate/{shop}', [
    ShopTranslationController::class, 'storeTranslation'
])->name('shop.translate.store');

Route::get('shop/translate/bulk', [
    ShopTranslationController::class, 'showBulkUploadForm'
])->name('shop.translate.bulk')->middleware('ajax');

Route::post('shop/translate/bulk', [
    ShopTranslationController::class, 'storeBulkTranslation'
])->name('shop.translate.bulk.store');

Route::post('shop/translate/upload', [
    ShopTranslationController::class, 'uploadBulkTranslation'
])->name('shop.translate.bulk.upload');

Route::post('shop/translate/import', [
    ShopTranslationController::class, 'importBulkTranslation'
])->name('shop.translate.import');

Route::get('shop/translation/download/template', [
    ShopTranslationController::class, 'downloadTemplate'
])->name('shop.translate.download.template');

Route::get('shop/translation/download/failedRows', [
    ShopTranslationController::class, 'downloadFailedRows'
])->name('shop.translate.download.failedRows');
