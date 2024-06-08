<?php

namespace App\Filament\Widgets;

use App\Models\Setting;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class TodayOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $range = range(1, 5);
        shuffle($range);

        return [
            Stat::make("Today's Total Purchases Due", $this->getTotalPurchasesDue())
                ->color('warning')
                ->chart($range),
            Stat::make("Today's Total Purchases Amount", $this->getTotalPurchasesAmount())
                ->color('success')
                ->chart($range),
            Stat::make("Today's Total Sales Due", $this->getTotalSalesDue())
                ->color('warning')
                ->chart($range),
            Stat::make("Today's Total Sales Amount", $this->getTotalSalesAmount())
                ->color('success')
                ->chart($range),
        ];
    }

    /**
     * @return string
     */
    protected function getTotalPurchasesDue(): string
    {
        return $this->formatCurrency($this->getTotalDue('purchase_orders'));
    }

    /**
     * @return string
     */
    protected function getTotalPurchasesAmount(): string
    {
        return $this->formatCurrency($this->getTotalAmount('purchase_orders'));
    }

    /**
     * @return string
     */
    protected function getTotalSalesDue(): string
    {
        return $this->formatCurrency($this->getTotalDue('sales'));
    }

    /**
     * @return string
     */
    protected function getTotalSalesAmount(): string
    {
        return $this->formatCurrency($this->getTotalAmount('sales'));
    }

    /**
     * @param  $tableName
     * @return string
     */
    protected function getTotalDue($tableName): string
    {
        $today = now()->toDateString();
        $column = $tableName === 'sales' ? 'sale_date' : 'order_date';

        return cache()->remember('widget-today-total-due-'.$tableName, 60 * 3, function () use ($tableName, $column, $today) {
            return DB::table($tableName)
                ->selectRaw('SUM(total_amount - paid_amount) as total_due')
                ->whereDate($column, $today)
                ->value('total_due') ?? 0;
        });
    }

    /**
     * @param  $tableName
     * @return string
     */
    protected function getTotalAmount($tableName): string
    {
        $today = now()->toDateString();
        $column = $tableName === 'sales' ? 'sale_date' : 'order_date';

        return cache()->remember('widget-today-total-amount-'.$tableName, 60 * 3, function () use ($tableName, $today, $column) {
            return DB::table($tableName)
                ->selectRaw('SUM(total_amount) as total_amount')
                ->whereDate($column, $today)
                ->value('total_amount') ?? 0;
        });
    }

    /**
     * @param  float  $amount
     * @return string
     */
    protected function formatCurrency(float $amount): string
    {
        return number_format($amount, 2).' '.Setting::getCurrency();
    }
}
