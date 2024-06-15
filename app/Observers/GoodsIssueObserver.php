<?php

namespace App\Observers;

use App\Events\GoodsIssueCreated;
use App\Models\GoodsIssue;
use Illuminate\Database\Eloquent\Model;

class GoodsIssueObserver extends BaseObserver
{
    /**
     * Handle the Model "creating" event.
     */
    public function creating(Model $model): void
    {
        if (auth()->check()) {
            $model->gin_code = GoodsIssue::generateCode();
            $model->user_id = auth()->user()->id;
        }
    }

    /**
     * @param  GoodsIssue  $goodsIssue
     * @return void
     */
    public function created(GoodsIssue $goodsIssue)
    {
        event(
            new GoodsIssueCreated(
                productId: $goodsIssue->product_id,
                quantity: $goodsIssue->quantity,
                userId: $goodsIssue->user_id,
                type: $goodsIssue->type,
                customerId: $goodsIssue->customer_id,
                supplierId: $goodsIssue->supplier_id,
                referenceNumber: ! empty($goodsIssue->sale_id) ? $goodsIssue->sale->invoice_number : $goodsIssue->gin_code, // if there is sale_id then use invoice_number
            )
        );
    }
}
