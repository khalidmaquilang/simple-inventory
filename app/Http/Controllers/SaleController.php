<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Sale;
use App\Services\SaleService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SaleController extends Controller
{
    public function __construct(protected SaleService $saleService)
    {
    }

    /**
     * @param  Company  $company
     * @param  Sale  $sale
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function generateInvoice(Company $company, Sale $sale): BinaryFileResponse
    {
        if ($company->id !== $sale->company_id) {
            abort(404);
        }

        return $this->saleService->generateInvoice($sale);
    }
}
