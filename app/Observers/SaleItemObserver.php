<?php

namespace App\Observers;

use App\Models\Sale;
use App\Models\SaleItem;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

class SaleItemObserver
{
    /**
     * Handle the Model "creating" event.
     */
    public function creating(Model $model): void
    {
        if (auth()->check()) {
            $model->company_id = Filament::getTenant()->id;
        }
    }

    /**
     * @param  Sale  $sale
     * @return void
     */
    public function created(SaleItem $saleItem)
    {
        $sale = $saleItem->sale;

        event(new \App\Events\SaleCreated(
            productId: $saleItem->product_id,
            quantity: $saleItem->quantity,
            unitCost: $saleItem->unit_cost,
            userId: $sale->user_id,
            customerId: $sale->customer_id,
            referenceNumber: $sale->invoice_number,
        ));
    }
}
