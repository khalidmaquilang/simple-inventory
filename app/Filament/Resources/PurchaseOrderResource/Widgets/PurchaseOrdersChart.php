<?php

namespace App\Filament\Resources\PurchaseOrderResource\Widgets;

use App\Models\PurchaseOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class PurchaseOrdersChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'purchaseOrdersChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Purchase Order This Month';

    protected static ?int $sort = 4;

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
        $purchaseOrders = cache()->remember('purchase_order_widget', now()->addMinutes(5), function () {
            return PurchaseOrder::select(
                DB::raw('DATE_FORMAT(order_date, "%e") as date'),
                DB::raw('SUM(total_amount) as sum_total_amount')
            )
                ->whereYear('order_date', Carbon::now()->year)
                ->whereMonth('order_date', Carbon::now()->month)
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->pluck('sum_total_amount', 'date')
                ->toArray();
        });

        $data = [];
        for ($day = 1; $day <= count($this->daysOfTheMonth()); $day++) {
            if (isset($purchaseOrders[$day])) {
                $data[] = (float) $purchaseOrders[$day];

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
