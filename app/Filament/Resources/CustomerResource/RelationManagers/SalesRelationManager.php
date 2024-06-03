<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SalesRelationManager extends RelationManager
{
    protected static string $relationship = 'sales';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number'),
                Tables\Columns\TextColumn::make('sale_date')
                    ->date(),
                Tables\Columns\TextColumn::make('total_amount'),
                Tables\Columns\TextColumn::make('paid_amount'),
                Tables\Columns\TextColumn::make('paymentType.name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn ($record) => route('filament.app.resources.sales.edit', $record)),
            ]);
    }
}
