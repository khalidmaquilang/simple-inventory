<?php

namespace App\Http\Controllers;

use App\Models\Company;
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
    public function generateInvoice(Company $company, Payment $payment): Response
    {
        return $this->paymentService->generateInvoice($payment);
    }
}
