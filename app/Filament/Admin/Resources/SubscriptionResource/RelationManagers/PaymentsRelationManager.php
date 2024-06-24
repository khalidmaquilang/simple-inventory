<?php

namespace App\Filament\Admin\Resources\SubscriptionResource\RelationManagers;

use App\Enums\PaymentStatusEnum;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;
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
                Tables\Columns\TextColumn::make('reference_number')
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
                Tables\Actions\Action::make('Pay Due')
                    ->label('')
                    ->tooltip('Pay Due')
                    ->icon('heroicon-o-banknotes')
                    ->color('info')
                    ->form([
                        Group::make([
                            TextInput::make('reference_number'),
                        ]),
                    ])
                    ->action(function ($record, array $data) {
                        $record->reference_number = $data['reference_number'];
                        $record->status = PaymentStatusEnum::SUCCESS;
                        $record->save();

                        $record->subscription->updateEndDate();
                    })
                    ->visible(fn ($record) => $record->status === PaymentStatusEnum::PENDING),
                Tables\Actions\Action::make('Download Invoice')
                    ->label('')
                    ->tooltip('Download Invoice')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->url(fn ($record) => route('admin.sales.generate-invoice', [$record]))
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
