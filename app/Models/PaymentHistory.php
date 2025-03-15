<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PaymentHistory extends Model
{
    use HasFactory;

    protected $table = 'payment_histories';

    protected $fillable = [
        'payable_id',
        'payable_type',
        'amount_paid',
        'remaining_balance',
        'payment_date',
    ];

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            $payable = $payment->payable;
            if ($payable) {
                $newRemainingBalance = max(0, $payable->total_amount - ($payable->paid_amount + $payment->amount_paid));
                $payment->remaining_balance = $newRemainingBalance;
            }
        });
    }
}
