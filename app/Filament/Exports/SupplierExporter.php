<?php

namespace App\Filament\Exports;

use App\Models\Supplier;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class SupplierExporter extends Exporter
{
    use ReportExporterTrait;

    protected string $filename = 'suppliers-report';

    protected static ?string $model = Supplier::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('company_name'),
            ExportColumn::make('contact_person'),
            ExportColumn::make('email'),
            ExportColumn::make('phone'),
            ExportColumn::make('status')
                ->formatStateUsing(fn ($state) => $state->getLabel()),
            ExportColumn::make('user.name'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your supplier export has completed and '.number_format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
