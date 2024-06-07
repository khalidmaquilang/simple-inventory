<?php

namespace App\Filament\Exports;

use App\Models\Sale;
use Carbon\Carbon;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class SaleExporter extends Exporter
{
    use ReportExporterTrait;

    protected string $filename = 'sales-report';

    protected static ?string $model = Sale::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('invoice_number'),
            ExportColumn::make('sale_date')
                ->formatStateUsing(fn ($state) => Carbon::parse($state)->format('M d, Y')),
            ExportColumn::make('pay_until')
                ->label('Due Date')
                ->formatStateUsing(fn ($state) => now()->addDays($state)->format('M d, Y')),
            ExportColumn::make('vat'),
            ExportColumn::make('discount'),
            ExportColumn::make('discount_type')
                ->formatStateUsing(fn ($state) => $state->getLabel()),
            ExportColumn::make('total_amount'),
            ExportColumn::make('paid_amount'),
            ExportColumn::make('customer.name'),
            ExportColumn::make('paymentType.name'),
            ExportColumn::make('user.name'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your sale export has completed and '.number_format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
