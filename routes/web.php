<?php

use App\Http\Controllers\SaleController;

Route::group(['middleware' => ['auth', \App\Http\Middleware\CompanyControllerGuard::class]], function () {
    Route::get('{company}/sales/{sale}/invoice', [SaleController::class, 'generateInvoice'])->name('sales.generate-invoice');
});
