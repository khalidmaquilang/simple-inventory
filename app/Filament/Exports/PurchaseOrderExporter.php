<?php

namespace App\Filament\Exports;

use App\Models\PurchaseOrder;
use Carbon\Carbon;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PurchaseOrderExporter extends Exporter
{
    use ReportExporterTrait;

    protected string $filename = 'purchase-orders-report';

    protected static ?string $model = PurchaseOrder::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('purchase_code'),
            ExportColumn::make('order_date')
                ->formatStateUsing(fn ($state) => Carbon::parse($state)->format('M d, Y')),
            ExportColumn::make('expected_delivery_date')
                ->formatStateUsing(fn ($state) => empty($state) ? '' : Carbon::parse($state)->format('M d, Y')),
            ExportColumn::make('status')
                ->formatStateUsing(fn ($state) => $state->getLabel() ?? ''),
            ExportColumn::make('total_amount'),
            ExportColumn::make('paid_amount'),
            ExportColumn::make('supplier.company_name'),
            ExportColumn::make('paymentType.name'),
            ExportColumn::make('user.name'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your purchase order export has completed and '.number_format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
