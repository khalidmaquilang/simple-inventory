<?php

namespace App\Filament\Exports;

use Filament\Actions\Exports\Models\Export;

trait ReportExporterTrait
{
    /**
     * @param  Export  $export
     * @return string
     */
    public function getFileName(Export $export): string
    {
        return "{$this->filename}-".now()->format('Y-m-d');
    }

    /**
     * @return string|null
     */
    public function getJobQueue(): ?string
    {
        return 'long-running-queue';
    }
}
