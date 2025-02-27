<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentHistory extends Model
{
    use HasFactory;

    protected $table = 'payment_histories';

    protected $fillable = [
        'purchase_order_id',
        'amount_paid',
        'remaining_balance',
        'payment_date',
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            $purchaseOrder = $payment->purchaseOrder;
            if ($purchaseOrder) {
                $newRemainingBalance = max(0, $purchaseOrder->remaining_amount - $payment->amount_paid);
                $purchaseOrder->update(['remaining_amount' => $newRemainingBalance]);
            }
        });
    }
}
