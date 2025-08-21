<?php

use App\Http\Controllers\AttachmentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['xssSanitizer'])->group(function () {
  Route::get('attachment/{attachment}/download', [
    AttachmentController::class,
    'download'
  ])->name('attachment.download');

  Route::delete('attachment/{attachment}', [
    AttachmentController::class,
    'destroy'
  ])->name('attachment.delete');
});
