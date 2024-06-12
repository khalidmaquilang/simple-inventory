<?php

namespace App\Filament\Admin\Resources\SubscriptionResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_date')
                    ->date(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('PHP'),
                Tables\Columns\TextColumn::make('payment_method'),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
            ])
            ->actions([
                Tables\Actions\Action::make('Download Invoice')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->url(fn ($record) => route('admin.sales.generate-invoice', [$record]))
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
