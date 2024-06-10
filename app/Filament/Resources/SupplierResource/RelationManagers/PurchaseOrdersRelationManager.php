<?php

namespace App\Filament\Resources\SupplierResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class PurchaseOrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'purchaseOrders';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('purchase_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('order_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money($this->getOwnerRecord()->company->getCurrency()),
                Tables\Columns\TextColumn::make('remaining_amount')
                    ->getStateUsing(function ($record): float {
                        return $record->total_amount - $record->paid_amount;
                    })
                    ->money($this->getOwnerRecord()->company->getCurrency()),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('user.name'),
            ])
            ->filters([
                DateRangeFilter::make('order_date'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }
}
