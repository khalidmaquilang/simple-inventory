<?php

namespace App\Filament\Exports;

use App\Models\StockMovement;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class StockMovementExporter extends Exporter
{
    use ReportExporterTrait;

    protected string $filename = 'stock-movement-report';

    protected static ?string $model = StockMovement::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('created_at'),
            ExportColumn::make('reference_number'),
            ExportColumn::make('quantity_before_adjustment'),
            ExportColumn::make('quantity'),
            ExportColumn::make('type')
                ->formatStateUsing(fn ($state) => $state->getLabel() ?? ''),
            ExportColumn::make('note'),
            ExportColumn::make('supplier.company_name'),
            ExportColumn::make('customer.name'),
            ExportColumn::make('user.name'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your stock movement export has completed and '.number_format($export->successful_rows).' '.str('row')
            ->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
