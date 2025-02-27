<?php

namespace App\Filament\Resources\PurchaseOrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentHistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'paymentHistories';

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('amount_paid')
                ->required()
                ->numeric()
                ->minValue(1),
            TextInput::make('remaining_balance')
                ->disabled(),
            Forms\Components\DatePicker::make('payment_date')
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('amount_paid')
                    ->sortable()
                    ->money(fn ($record) => $record->purchaseOrder->company->getCurrency()),
                Tables\Columns\TextColumn::make('remaining_balance')
                    ->sortable()
                    ->money(fn ($record) => $record->purchaseOrder->company->getCurrency()),
                Tables\Columns\TextColumn::make('payment_date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('payment_date', 'desc');
    }
}
