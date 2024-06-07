<?php

namespace App\Observers;

use App\Models\Sale;
use Illuminate\Database\Eloquent\Model;

class SaleObserver extends BaseObserver
{
    /**
     * Handle the Model "creating" event.
     */
    public function creating(Model $model): void
    {
        if (auth()->check()) {
            $model->invoice_number = Sale::generateCode();
            $model->user_id = auth()->user()->id;
        }
    }
}
