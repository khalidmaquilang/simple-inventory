<?php

use App\Http\Controllers\SaleController;

Route::group(['middleware' => ['auth']], function () {
    Route::get('/sales/{sale}/invoice', [SaleController::class, 'generateInvoice'])->name('sales.generate-invoice');
});
