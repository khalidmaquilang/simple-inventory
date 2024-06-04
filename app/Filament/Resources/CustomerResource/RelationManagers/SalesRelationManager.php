<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use App\Models\Sale;
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
                Tables\Actions\Action::make('Download Invoice')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->url(fn (Sale $record) => route('sales.generate-invoice', $record))
                    ->openUrlInNewTab(),
            ]);
    }
}
