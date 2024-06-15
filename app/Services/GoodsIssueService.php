<?php

namespace App\Services;

use App\Models\GoodsIssue;
use App\Repositories\GoodsIssueRepository;
use Illuminate\Support\Facades\Log;

class GoodsIssueService
{
    /**
     * @param  GoodsIssueRepository  $goodsIssueRepository
     */
    public function __construct(protected GoodsIssueRepository $goodsIssueRepository)
    {
    }

    /**
     * @param  array  $data
     * @return GoodsIssue|null
     */
    public function store(array $data): ?GoodsIssue
    {
        try {
            return $this->goodsIssueRepository->create([
                'company_id' => $data['company_id'],
                'sale_id' => $data['sale_id'] ?? null,
                'user_id' => $data['user_id'],
                'customer_id' => $data['customer_id'] ?? null,
                'supplier_id' => $data['supplier_id'] ?? null,
                'issue_date' => $data['issue_date'],
                'sku' => $data['sku'],
                'name' => $data['name'],
                'quantity' => $data['quantity'],
                'product_id' => $data['product_id'],
                'type' => $data['type'],
                'notes' => $data['notes'] ?? null,
            ]);
        } catch (\Exception $exception) {
            Log::error('There was something wrong while storing goods issue.', [
                'data' => $data,
                'user_id' => auth()->id(),
                'company_id' => filament()->getTenant()->id,
            ]);

            abort(500, $exception->getMessage());
        }
    }
}
