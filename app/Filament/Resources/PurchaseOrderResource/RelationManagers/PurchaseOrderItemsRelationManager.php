<?php

namespace App\Filament\Resources\PurchaseOrderResource\RelationManagers;

use Filament\Facades\Filament;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PurchaseOrderItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'purchaseOrderItems';

    public function table(Table $table): Table
    {
        $currency = Filament::getTenant()->getCurrency();

        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Product Name'),
                Tables\Columns\TextColumn::make('quantity'),
                Tables\Columns\TextColumn::make('unit_cost')
                    ->money(fn ($record) => $record->company->getCurrency()),
                Tables\Columns\TextColumn::make('quantity_received'),
                Tables\Columns\TextColumn::make('remaining_quantity')
                    ->getStateUsing(function ($record): int {
                        return $record->quantity - $record->quantity_received;
                    })
                    ->money(fn ($record) => $record->company->getCurrency()),
            ])
            ->filters([
                //
            ]);
    }
}
