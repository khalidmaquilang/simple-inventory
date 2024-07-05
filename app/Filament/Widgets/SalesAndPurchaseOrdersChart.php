<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Traits\ChartFilterTrait;
use App\Models\PurchaseOrder;
use App\Models\Sale;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\TrendValue;

class SalesAndPurchaseOrdersChart extends ChartWidget
{
    use ChartFilterTrait, HasWidgetShield;

    protected static ?string $heading = 'Sales And Purchase Orders Chart';

    public ?string $filter = 'today';

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '300px';

    protected static ?int $sort = 8;

    /**
     * @return array|mixed[]
     */
    protected function getData(): array
    {
        $activeFilter = $this->filter;

        $companyId = filament()->getTenant()->id;

        $purchaseOrder = $this->cacheTrend(
            "purchase_order_filter_widget_{$companyId}_{$activeFilter}",
            fn () => $this->getTrendByFilter(PurchaseOrder::class, $activeFilter, 'total_amount', 'order_date')
        );
        $saleOrder = $this->cacheTrend(
            "sales_filter_widget_{$companyId}_{$activeFilter}",
            fn () => $this->getTrendByFilter(Sale::class, $activeFilter, 'total_amount', 'sale_date')
        );

        return [
            'datasets' => [
                [
                    'label' => 'Purchase Orders',
                    'data' => $purchaseOrder->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#4B0082',
                    'borderColor' => '#8A2BE2',
                ],
                [
                    'label' => 'Sale Orders',
                    'data' => $saleOrder->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#FFA500',
                    'borderColor' => '#A0522D',
                ],
            ],
            'labels' => $purchaseOrder->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
