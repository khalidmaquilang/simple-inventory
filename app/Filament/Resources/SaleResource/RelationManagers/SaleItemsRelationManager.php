<?php

namespace App\Filament\Resources\SaleResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SaleItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'saleItems';

    public function table(Table $table): Table
    {
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
                Tables\Columns\TextColumn::make('total_amount')
                    ->getStateUsing(function ($record): float {
                        return $record->quantity * $record->unit_cost;
                    })
                    ->money(filament()->getTenant()->getCurrency()),
            ]);
    }
}
