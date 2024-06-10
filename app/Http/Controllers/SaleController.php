<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\Response;

class SaleController extends Controller
{
    public function __construct(protected SaleService $saleService)
    {
    }

    /**
     * @param  Sale  $sale
     * @return Response
     */
    public function generateInvoice(Company $company, Sale $sale): Response
    {
        if ($company->id !== $sale->company_id) {
            abort(404);
        }

        return $this->saleService->generateInvoice($sale);
    }
}
