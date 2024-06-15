<?php

namespace App\Repositories;

use App\Models\GoodsIssue;

class GoodsIssueRepository extends BaseRepository
{
    /**
     * @param  GoodsIssue  $model
     */
    public function __construct(GoodsIssue $model)
    {
        $this->model = $model;
    }

    /**
     * @param  array  $data
     * @return GoodsIssue
     */
    public function create(array $data): GoodsIssue
    {
        return $this->model->create([
            'company_id' => $data['company_id'],
            'sale_id' => $data['sale_id'],
            'user_id' => $data['user_id'],
            'customer_id' => $data['customer_id'],
            'supplier_id' => $data['supplier_id'],
            'issue_date' => $data['issue_date'],
            'sku' => $data['sku'],
            'name' => $data['name'],
            'quantity' => $data['quantity'],
            'product_id' => $data['product_id'],
            'type' => $data['type'],
            'notes' => $data['notes'],
        ]);
    }
}
