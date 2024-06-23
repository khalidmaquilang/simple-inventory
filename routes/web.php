<?php

use App\Filament\Pages\RegisterInvited;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\SaleController;
use App\Http\Middleware\CompanyControllerGuard;

Route::redirect('/login-redirect', '/login')->name('login');

Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::group(['middleware' => [CompanyControllerGuard::class]], function () {
        Route::get('{company}/sales/{sale}/invoice', [SaleController::class, 'generateInvoice'])->name(
            'app.sales.generate-invoice'
        );

        Route::get('{company}/payments/{payment}/invoice', [\App\Http\Controllers\PaymentController::class, 'generateInvoice'])->name(
            'app.payments.generate-invoice'
        );
    });

    Route::get(
        '/admin/payments/{payment}/invoice',
        [PaymentController::class, 'generateInvoice']
    )->name('admin.sales.generate-invoice');
});

Route::get('invite-user/register', RegisterInvited::class)
    ->name('register.user-invite')
    ->middleware(['signed']);
