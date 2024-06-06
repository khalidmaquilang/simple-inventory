<?php

namespace App\Observers;

use App\Enums\PurchaseOrderEnum;
use App\Models\PurchaseOrder;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderObserver extends BaseObserver
{
    /**
     * Handle the Model "creating" event.
     */
    public function creating(Model $model): void
    {
        if (auth()->check()) {
            $model->purchase_code = PurchaseOrder::generateCode();
            $model->status = PurchaseOrderEnum::PENDING->value;
            $model->user_id = auth()->user()->id;
        }
    }
}
