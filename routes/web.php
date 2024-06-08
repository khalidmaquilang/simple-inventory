<?php

use App\Http\Controllers\SaleController;

Route::group(['middleware' => ['auth', \App\Http\Middleware\CompanyControllerGuard::class]], function () {
    Route::get('{company}/sales/{sale}/invoice', [SaleController::class, 'generateInvoice'])->name('sales.generate-invoice');
});

Route::get('invite-user/register', \App\Filament\Pages\RegisterInvited::class)
    ->name('register.user-invite')
    ->middleware(['signed', 'guest']);
