<?php

namespace App\Providers;

use App\Listeners\StockMovementSubscriber;
use App\Models\Customer;
use App\Models\GoodsReceipt;
use App\Models\Inventory;
use App\Models\Payment;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Observers\CustomerObserver;
use App\Observers\GoodsReceiptObserver;
use App\Observers\InventoryObserver;
use App\Observers\PaymentObserver;
use App\Observers\PurchaseOrderItemObserver;
use App\Observers\PurchaseOrderObserver;
use App\Observers\SaleItemObserver;
use App\Observers\SaleObserver;
use App\Observers\StockMovementObserver;
use App\Observers\SupplierObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment() === 'production') {
            URL::forceScheme('https');
        }
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
        PurchaseOrderItem::observe(PurchaseOrderItemObserver::class);
        Sale::observe(SaleObserver::class);
        SaleItem::observe(SaleItemObserver::class);
        Inventory::observe(InventoryObserver::class);
        StockMovement::observe(StockMovementObserver::class);
        GoodsReceipt::observe(GoodsReceiptObserver::class);
        Payment::observe(PaymentObserver::class);

        Event::subscribe(StockMovementSubscriber::class);
    }
}
