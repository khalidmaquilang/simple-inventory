<?php

namespace App\Providers;

use App\Models\Customer;
use App\Models\PurchaseOrder;
use App\Models\Sale;
use App\Models\Supplier;
use App\Observers\CustomerObserver;
use App\Observers\PurchaseOrderObserver;
use App\Observers\SaleObserver;
use App\Observers\SupplierObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();
        Customer::observe(CustomerObserver::class);
        Supplier::observe(SupplierObserver::class);
        PurchaseOrder::observe(PurchaseOrderObserver::class);
        Sale::observe(SaleObserver::class);
    }
}
