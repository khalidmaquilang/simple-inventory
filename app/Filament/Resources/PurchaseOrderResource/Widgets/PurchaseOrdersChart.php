<?php

namespace App\Filament\Resources\PurchaseOrderResource\Widgets;

use App\Filament\Widgets\Traits\ChartFilterTrait;
use App\Models\PurchaseOrder;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\TrendValue;

class PurchaseOrdersChart extends ChartWidget
{
    use ChartFilterTrait, HasWidgetShield;

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Purchase Orders';

    public ?string $filter = 'today';

    protected static ?int $sort = 4;

    /**
     * @return array|mixed[]
     */
    protected function getData(): array
    {
        $activeFilter = $this->filter;

        $companyId = filament()->getTenant()->id;

        $purchaseOrder = $this->cacheTrend(
            "purchase_order_widget_{$companyId}_{$activeFilter}",
            fn () => $this->getTrendByFilter(PurchaseOrder::class, $activeFilter, 'total_amount', 'order_date')
        );

        return [
            'datasets' => [
                [
                    'label' => 'Purchase Orders',
                    'data' => $purchaseOrder->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $purchaseOrder->map(fn (TrendValue $value) => $value->date),
        ];
    }

    /**
     * @return string
     */
    protected function getType(): string
    {
        return 'line';
    }
}
