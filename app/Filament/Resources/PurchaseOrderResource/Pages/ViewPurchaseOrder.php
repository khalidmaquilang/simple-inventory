<?php

namespace App\Filament\Resources\PurchaseOrderResource\Pages;

use App\Filament\Resources\PurchaseOrderResource;
use Filament\Actions\Action;
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
                                TextEntry::make('shipping_fee')
                                    ->formatStateUsing(fn ($state) => number_format($state, 2).' '.$currency),
                                TextEntry::make('total_amount')
                                    ->formatStateUsing(fn ($state) => number_format($state, 2).' '.$currency),
                                TextEntry::make('paid_amount')
                                    ->formatStateUsing(fn ($state) => number_format($state, 2).' '.$currency),
                                TextEntry::make('paymentType.name'),
                                TextEntry::make('reference_number'),
                            ])
                            ->columns(3),
                    ]),

            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Complete')
                ->requiresConfirmation()
                ->color('success')
                ->icon('heroicon-m-check')
                ->action(fn ($record) => $record->setCompleted())
                ->visible(fn ($record) => $record->isAvailable()),
            Action::make('Cancel')
                ->requiresConfirmation()
                ->color('danger')
                ->icon('heroicon-m-x-mark')
                ->action(fn ($record) => $record->setCancelled())
                ->visible(fn ($record) => $record->isAvailable()),
        ];
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
