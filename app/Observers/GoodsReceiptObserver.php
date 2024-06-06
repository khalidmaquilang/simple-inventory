<?php

namespace App\Observers;

use App\Enums\PurchaseOrderEnum;
use App\Enums\StockMovementEnum;
use App\Models\GoodsReceipt;
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
            userId: $goodsReceipt->user_id,
            type: StockMovementEnum::PURCHASE,
            referenceNumber: $goodsReceipt->grn_code,
            supplierId: $goodsReceipt->purchaseOrder->supplier_id,
        ));

        $goodsReceipt->purchaseOrder->update([
            'status' => PurchaseOrderEnum::PARTIALLY_RECEIVED->value,
        ]);
    }
}
