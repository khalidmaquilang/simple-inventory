<?php

namespace App\Repositories;

use App\Models\PaymentHistory;

class PaymentHistoryRepository
{
    public function createPaymentHistory($record, array $data)
    {
        return PaymentHistory::create([
            'payable_id' => $record->id,
            'payable_type' => get_class($record),
            'amount_paid' => $data['paid_amount'],
            'remaining_balance' => max(0, $record->remaining_amount - $data['paid_amount']),
            'payment_date' => now(),
        ]);
    }
}
