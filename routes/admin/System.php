<?php

use App\Http\Controllers\Admin\ApplicationKeyController;
use App\Http\Controllers\Admin\SystemController;
use Illuminate\Support\Facades\Route;

// system
Route::name('system.')->prefix('system')->group(function () {
  Route::put('maintenanceMode/toggle', [
    SystemController::class,
    'toggleMaintenanceMode'
  ])->name('maintenanceMode.toggle')->middleware('ajax');

  Route::get('general', [
    SystemController::class,
    'view'
  ])->name('general');

  Route::put('updateBasicSystem', [
    SystemController::class,
    'update'
  ])->name('basicUpdate');

  Route::get('modifyEnvironment', [
    SystemController::class,
    'modifyEnvFile'
  ])->name('modifyEnvFile')->middleware('ajax');

  Route::post('modifyEnvironment', [
    SystemController::class,
    'saveEnvFile'
  ])->name('saveEnvFile');

  Route::get('importDemoContents', [
    SystemController::class,
    'importDemoContents'
  ])->name('importDemoContents')->middleware('ajax');

  Route::post('importDemoContents', [
    SystemController::class,
    'resetDatabase'
  ])->name('reset');

  Route::get('clearDemoContents', [
    SystemController::class,
    'clearDemoConfirmation'
  ])->name('clearDemoConfirmation');

  Route::post('clearDemoContents', [
    SystemController::class,
    'clearDemoContents'
  ])->name('clearDemoContents');

  Route::get('backup', [
    SystemController::class,
    'backup'
  ])->name('backup');

  // License
  Route::get('license/uninstall', [
    SystemController::class,
    'uninstallLicense'
  ])->name('license.uninstall')->middleware('ajax');

  Route::post('license/uninstall', [
    SystemController::class,
    'uninstallLicense'
  ])->name('license.reset');
});

// Application key for mobile app
Route::get('generate-key', [
  ApplicationKeyController::class,
  'confirm'
])->name('key.confirm');

Route::post('generate-key', [
  ApplicationKeyController::class,
  'generate'
])->name('key.generate');
