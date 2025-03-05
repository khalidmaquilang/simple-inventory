<?php

namespace App\Filament\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentHistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'paymentHistories';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('amount_paid')
                    ->sortable()
                    ->money(fn ($record) => $record->payable->getCurrency()),
                Tables\Columns\TextColumn::make('remaining_balance')
                    ->sortable()
                    ->money(fn ($record) => $record->payable->getCurrency()),
                Tables\Columns\TextColumn::make('payment_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payable_type')
                    ->label('Payment For')
                    ->sortable(),
            ])
            ->filters([])
            ->headerActions([])
            ->actions([])
            ->defaultSort('payment_date', 'desc');
    }
}
