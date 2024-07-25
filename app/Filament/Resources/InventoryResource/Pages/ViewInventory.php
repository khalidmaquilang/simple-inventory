<?php

namespace App\Filament\Resources\InventoryResource\Pages;

use App\Filament\Resources\InventoryResource;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewInventory extends ViewRecord
{
    protected static string $resource = InventoryResource::class;

    protected $listeners = ['refresh' => '$refresh'];

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Inventory Details')
                    ->columns(4)
                    ->schema([
                        TextEntry::make('product.name'),
                        TextEntry::make('quantity_on_hand')
                            ->formatStateUsing(fn ($state, $record) => "{$record->getQuantityUnit()}"),
                        TextEntry::make('formatted_average_cost')
                            ->label('Average Cost'),
                        TextEntry::make('product.reorder_point')
                            ->label('Reorder Point'),
                    ]),
            ]);
    }

    public function getRelationManagers(): array
    {
        return [
            InventoryResource\RelationManagers\StockMovementsRelationManager::class,
        ];
    }
}
