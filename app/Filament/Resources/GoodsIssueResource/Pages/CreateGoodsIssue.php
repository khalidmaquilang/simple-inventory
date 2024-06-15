<?php

namespace App\Filament\Resources\GoodsIssueResource\Pages;

use App\Filament\Resources\GoodsIssueResource;
use App\Models\Product;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt;

class CreateGoodsIssue extends CreateRecord
{
    protected static string $resource = GoodsIssueResource::class;

    /**
     * @param  array  $data
     * @return array|mixed[]
     *
     * @throws Halt
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $product = Product::find($data['product_id']);
        if (empty($product)) {
            $this->haltProcess();
        }

        $data['sku'] = $product->sku;
        $data['name'] = $product->name;

        return $data;
    }

    /**
     * @return void
     *
     * @throws \Filament\Support\Exceptions\Halt
     */
    protected function haltProcess(): Halt
    {
        Notification::make()
            ->warning()
            ->title('Oooppss! Product doesn\'t exist.')
            ->body('Please Refresh Page.')
            ->send();

        $this->halt();
    }
}
