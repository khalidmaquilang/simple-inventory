<?php

namespace App\Filament\Resources\SaleResource\Widgets;

use App\Filament\Widgets\Traits\ChartFilterTrait;
use App\Models\Sale;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\TrendValue;

class SalesChart extends ChartWidget
{
    use ChartFilterTrait, HasWidgetShield;

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Sales';

    public ?string $filter = 'today';

    protected static ?int $sort = 5;

    /**
     * @return array
     */
    protected function getData(): array
    {
        $activeFilter = $this->filter;

        $companyId = filament()->getTenant()->id;

        $saleOrder = $this->cacheTrend(
            "sales_filter_widget_{$companyId}_{$activeFilter}",
            fn () => $this->getTrendByFilter(Sale::class, $activeFilter, 'total_amount', 'sale_date')
        );

        return [
            'datasets' => [
                [
                    'label' => 'Sales',
                    'data' => $saleOrder->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $saleOrder->map(fn (TrendValue $value) => $value->date),
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
