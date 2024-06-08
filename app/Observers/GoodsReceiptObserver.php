<?php

namespace App\Observers;

use App\Enums\PurchaseOrderEnum;
use App\Models\GoodsReceipt;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

class GoodsReceiptObserver extends BaseObserver
{
    /**
     * Handle the Model "creating" event.
     */
    public function creating(Model $model): void
    {
        if (auth()->check()) {
            $model->grn_code = GoodsReceipt::generateCode();
            $model->user_id = auth()->user()->id;
            $model->company_id = Filament::getTenant()->id;
        }
    }

    /**
     * @param  GoodsReceipt  $goodsReceipt
     * @return void
     */
    public function created(GoodsReceipt $goodsReceipt)
    {
        event(new \App\Events\GoodsReceiptCreated(
            productId: $goodsReceipt->product_id,
            quantity: $goodsReceipt->quantity,
            unitCost: $goodsReceipt->unit_cost,
            userId: $goodsReceipt->user_id,
            supplierId: $goodsReceipt->purchaseOrder->supplier_id,
            referenceNumber: $goodsReceipt->grn_code,
        ));

        $goodsReceipt->purchaseOrder->update([
            'status' => PurchaseOrderEnum::PARTIALLY_RECEIVED->value,
        ]);
    }
}
