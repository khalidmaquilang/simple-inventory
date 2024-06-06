<?php

namespace App\Observers;

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
        event(new \App\Events\GoodsReceiptCreated($goodsReceipt));
    }
}
