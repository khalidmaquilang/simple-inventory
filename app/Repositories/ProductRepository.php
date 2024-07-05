<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductRepository extends BaseRepository
{
    /**
     * @param  Product  $model
     */
    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    /**
     * @return mixed
     */
    public function getTopSellingProducts(): mixed
    {
        return $this->model
            ->withoutGlobalScopes()
            ->select(
                'products.*',
                DB::raw(
                    'SUM(sale_items.quantity) as total_quantity_sold, SUM(sale_items.quantity * sale_items.unit_cost) as total_revenue'
                )
            )
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->where('sale_items.company_id', filament()->getTenant()->id)
            ->groupBy('products.id')
            ->limit(10)
            ->orderByDesc('total_quantity_sold');
    }
}
