<?php

namespace App\Observers;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Model;

class PaymentObserver extends BaseObserver
{
    /**
     * Handle the Model "creating" event.
     */
    public function creating(Model $model): void
    {
        $model->invoice_number = Payment::generateCode();
    }
}
