<?php

namespace App\Observers;

use App\Models\Sale;
use App\Models\SaleItem;

class SaleItemObserver
{
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
