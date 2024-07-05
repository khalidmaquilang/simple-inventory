<?php

namespace App\Filament\Pages;

use App\Models\Payment;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class Subscriptions extends Page implements HasInfolists, HasTable
{
    use HasPageShield, InteractsWithInfolists, InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.subscriptions';

    public function subscriptionList(Infolist $infolist): Infolist
    {
        return $infolist
            ->record(filament()->getTenant())
            ->schema([
                \Filament\Infolists\Components\Section::make()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('plan')
                            ->label('Current Subscribed Plan')
                            ->getStateUsing(fn ($record) => $record->getActiveSubscription()->plan->name),
                        TextEntry::make('status')
                            ->badge()
                            ->getStateUsing(fn ($record) => $record->getActiveSubscription()->status),
                        TextEntry::make('next_billing_cycle')
                            ->date()
                            ->getStateUsing(fn ($record) => $record->getActiveSubscription()->end_date),
                        Actions::make([
                            \Filament\Infolists\Components\Actions\Action::make('changeSubscriptionPlan')
                                ->action(fn () => redirect()->route('filament.app.pages.plans', filament()->getTenant())),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Invoices')
            ->query(Payment::query())
            ->columns([
                TextColumn::make('invoice_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('payment_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('amount')
                    ->money('PHP')
                    ->sortable(),
                TextColumn::make('payment_method'),
                TextColumn::make('status')
                    ->badge(),
            ])
            ->actions([
                Action::make('Download Invoice')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->url(fn ($record) => route('app.payments.generate-invoice', [
                        'company' => filament()->getTenant()->id,
                        'payment' => $record,
                    ]))
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
