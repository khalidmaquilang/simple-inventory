<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Response;

class PaymentController extends Controller
{
    public function __construct(protected PaymentService $paymentService)
    {
    }

    /**
     * @param  Payment  $payment
     * @return Response
     */
    public function generateInvoice(Payment $payment): Response
    {
        return $this->paymentService->generateInvoice($payment);
    }
}
