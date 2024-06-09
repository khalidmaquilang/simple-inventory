<?php

namespace App\Filament\Resources\PurchaseOrderResource\Pages;

use App\Filament\Resources\PurchaseOrderResource;
use App\Models\Setting;
use Filament\Facades\Filament;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\FontWeight;

class ViewPurchaseOrder extends ViewRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    protected $listeners = ['refresh' => '$refresh'];

    public function infolist(Infolist $infolist): Infolist
    {
        $currency = Filament::getTenant()->getCurrency();

        return $infolist
            ->schema([
                Section::make('Purchase Order Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('purchase_code')
                            ->size(TextEntry\TextEntrySize::Medium)
                            ->weight(FontWeight::Bold),
                        TextEntry::make('status')
                            ->badge(),
                        TextEntry::make('order_date')
                            ->date(),
                        TextEntry::make('expected_delivery_date')
                            ->date(),
                        TextEntry::make('supplier.company_name'),
                        Fieldset::make('Payment Information')
                            ->schema([
                                TextEntry::make('total_amount')
                                    ->formatStateUsing(fn ($state) => number_format($state, 2).' '.$currency),
                                TextEntry::make('paid_amount')
                                    ->formatStateUsing(fn ($state) => number_format($state, 2).' '.$currency),
                                TextEntry::make('paymentType.name'),
                            ]),
                    ]),

            ]);
    }

    /**
     * @return string[]
     */
    public function getRelationManagers(): array
    {
        return [
            PurchaseOrderResource\RelationManagers\PurchaseOrderItemsRelationManager::class,
            PurchaseOrderResource\RelationManagers\GoodsReceiptsRelationManager::class,
        ];
    }
}
