<?php

namespace App\Filament\Widgets;

use App\Models\Setting;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class TotalOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $range = range(1, 5);
        shuffle($range);

        return [
            Stat::make('Total Purchases Due', $this->getTotalPurchasesDue())
                ->color('warning')
                ->chart($range),
            Stat::make('Total Purchases Amount', $this->getTotalPurchasesAmount())
                ->color('success')
                ->chart($range),
            Stat::make('Total Sales Due', $this->getTotalSalesDue())
                ->color('warning')
                ->chart($range),
            Stat::make('Total Sales Amount', $this->getTotalSalesAmount())
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
        return cache()->remember('widget-total-due-'.$tableName.'-'.Filament::getTenant()->id, 60 * 3, function () use ($tableName) {
            return DB::table($tableName)
                ->selectRaw('SUM(total_amount - paid_amount) as total_due')
                ->where('company_id', Filament::getTenant()->id)
                ->value('total_due') ?? 0;
        });
    }

    /**
     * @param  $tableName
     * @return string
     */
    protected function getTotalAmount($tableName): string
    {
        return cache()->remember('widget-total-amount-'.$tableName.'-'.Filament::getTenant()->id, 60 * 3, function () use ($tableName) {
            return DB::table($tableName)
                ->selectRaw('SUM(total_amount) as total_amount')
                ->where('company_id', Filament::getTenant()->id)
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
