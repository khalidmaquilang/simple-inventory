<?php

namespace App\Filament\Resources\SaleResource\Widgets;

use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class SalesChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'salesChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Sales This Month';

    protected static ?int $sort = 5;

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            'chart' => [
                'type' => 'line',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'Total Amount',
                    'data' => $this->getTotalAmountPerDay(),
                ],
            ],
            'xaxis' => [
                'categories' => $this->daysOfTheMonth(),
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#f59e0b'],
            'stroke' => [
                'curve' => 'smooth',
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getTotalAmountPerDay(): array
    {
        $salesOrder = cache()->remember('sales_order_widget', now()->addMinutes(5), function () {
            return Sale::select(
                DB::raw('DATE_FORMAT(sale_date, "%e") as date'),
                DB::raw('SUM(total_amount) as sum_total_amount')
            )
                ->whereYear('sale_date', Carbon::now()->year)
                ->whereMonth('sale_date', Carbon::now()->month)
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->pluck('sum_total_amount', 'date')
                ->toArray();
        });

        $data = [];
        for ($day = 1; $day <= count($this->daysOfTheMonth()); $day++) {
            if (isset($salesOrder[$day])) {
                $data[] = (float) $salesOrder[$day];

                continue;
            }

            $data[] = 0;
        }

        return $data;
    }

    /**
     * @return array
     */
    protected function daysOfTheMonth(): array
    {
        $now = Carbon::now();
        $daysInMonth = $now->daysInMonth;

        $daysArray = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $daysArray[] = $day;
        }

        return $daysArray;
    }
}
