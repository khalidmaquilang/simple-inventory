<?php

namespace App\Filament\Resources\SaleResource\Pages;

use App\Filament\Resources\SaleResource;
use App\Models\Sale;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;

class EditSale extends EditRecord
{
    protected static string $resource = SaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * @param  array  $data
     * @return array|mixed[]
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $sale = Sale::find($data['id']);
        $data['sub_total'] = $sale->getSubTotal();

        return $data;
    }
}