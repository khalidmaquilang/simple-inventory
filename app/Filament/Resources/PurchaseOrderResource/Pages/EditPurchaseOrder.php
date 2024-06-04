<?php

namespace App\Filament\Resources\PurchaseOrderResource\Pages;

use App\Filament\Resources\PurchaseOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;

class EditPurchaseOrder extends EditRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
