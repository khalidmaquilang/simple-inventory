<?php

namespace App\Filament\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
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
            DatePicker::make('payment_date')
                ->required(),
        ]);
    }

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
