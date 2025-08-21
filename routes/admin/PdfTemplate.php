<?php

use App\Http\Controllers\Admin\PdfTemplateController;
use Illuminate\Support\Facades\Route;

Route::middleware(['userType:admin'])->group(function () {
    Route::resource('pdfTemplate', PdfTemplateController::class);

    Route::get('download/{pdfTemplate}', [
        PdfTemplateController::class,
        'download'
    ])->name('pdfTemplate.download');
});
