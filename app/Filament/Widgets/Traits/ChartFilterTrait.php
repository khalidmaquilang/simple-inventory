<?php

namespace App\Filament\Widgets\Traits;

use Flowframe\Trend\Trend;
use Illuminate\Database\Eloquent\Builder;

trait ChartFilterTrait
{
    /**
     * @return string[]|null
     */
    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }

    /**
     * @param  $modelClass
     * @param  string  $filter
     * @param  string  $column
     * @param  string  $dateColumn
     * @return \Illuminate\Support\Collection
     */
    protected function getTrendByFilter(
        $modelClass,
        string $filter,
        string $column,
        string $dateColumn = 'created_at'
    ): \Illuminate\Support\Collection {
        return $this->filter($filter, Trend::model($modelClass), $column, $dateColumn);
    }

    protected function getTrendQueryByFilter(
        Builder $query,
        string $filter,
        string $column,
        string $dateColumn = 'created_at'
    ): \Illuminate\Support\Collection {
        $trend = Trend::query($query);

        return $this->filter($filter, $trend, $column, $dateColumn);
    }

    /**
     * @param  string  $filter
     * @param  Trend  $trend
     * @param  string  $column
     * @param  string  $dateColumn
     * @return \Illuminate\Support\Collection
     */
    protected function filter(
        string $filter,
        Trend $trend,
        string $column,
        string $dateColumn = 'created_at'
    ): \Illuminate\Support\Collection {
        if ($filter === 'today') {
            return $trend
                ->dateColumn($dateColumn)
                ->between(now()->startOfDay(), now()->endOfDay())
                ->perHour()
                ->sum($column);
        }

        if ($filter === 'week') {
            return $trend
                ->dateColumn($dateColumn)
                ->between(now()->subWeek(), now())
                ->perDay()
                ->sum($column);
        }

        if ($filter === 'month') {
            return $trend
                ->dateColumn($dateColumn)
                ->between(now()->subMonth(), now())
                ->perDay()
                ->sum($column);
        }

        return $trend
            ->dateColumn($dateColumn)
            ->between(now()->startOfYear(), now())
            ->perMonth()
            ->sum($column);
    }

    /**
     * @param  string  $key
     * @param  callable  $callback
     * @return mixed
     */
    protected function cacheTrend(string $key, callable $callback): mixed
    {
        return cache()->remember(
            $key,
            now()->addMinutes(3),
            $callback
        );
    }

    /**
     * @return array[]
     */
    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
