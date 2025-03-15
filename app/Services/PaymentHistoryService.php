<?php

namespace App\Services;

use App\Repositories\PaymentHistoryRepository;

class PaymentHistoryService
{
    protected PaymentHistoryRepository $paymentHistoryRepository;

    public function __construct(PaymentHistoryRepository $paymentHistoryRepository)
    {
        $this->paymentHistoryRepository = $paymentHistoryRepository;
    }

    public function recordPayment($record, array $data)
    {
        return $this->paymentHistoryRepository->createPaymentHistory($record, $data);
    }
}
