<?php

namespace App\Filament\Exports;

use Filament\Actions\Exports\Models\Export;

trait ReportExporterTrait
{
    public function getFileName(Export $export): string
    {
        return "{$this->filename}-".now()->format('Y-m-d');
    }
}
