<?php

namespace App\Filament\Resources\SaleResource\Pages;

use App\Filament\Resources\SaleResource;
use App\Models\Sale;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\FontWeight;

class ViewSales extends ViewRecord
{
    protected static string $resource = SaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Download Invoice')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->url(fn (Sale $record) => route('app.sales.generate-invoice', [
                    'company' => filament()->getTenant()->id,
                    'sale' => $record,
                ]))
                ->openUrlInNewTab(),
            Action::make('Pay Due Amount')
                ->form(Sale::getPayDueAmountForm())
                ->color('info')
                ->icon('heroicon-m-banknotes')
                ->visible(fn ($record) => $record->remaining_amount > 0)
                ->action(function ($record, array $data) {
                    $record->paid_amount += $data['paid_amount'];
                    $record->reference_number = $data['reference_number'];
                    $record->save();
                }),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        $currency = Filament::getTenant()->getCurrency();

        return $infolist
            ->schema([
                Section::make('Sales Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('invoice_number')
                            ->size(TextEntry\TextEntrySize::Medium)
                            ->weight(FontWeight::Bold),
                        TextEntry::make('sale_date')
                            ->date(),
                        TextEntry::make('pay_until')
                            ->label('Due Date')
                            ->formatStateUsing(fn ($state) => now()->addDays($state)->format('M d, Y')),
                        TextEntry::make('customer.name'),
                        Fieldset::make('Payment Information')
                            ->schema([
                                TextEntry::make('shipping_fee')
                                    ->formatStateUsing(fn ($state) => number_format($state, 2).' '.$currency),
                                TextEntry::make('vat'),
                                TextEntry::make('formatted_discount')
                                    ->label('Discount'),
                                TextEntry::make('total_amount')
                                    ->formatStateUsing(fn ($state) => number_format($state, 2).' '.$currency),
                                TextEntry::make('paid_amount')
                                    ->formatStateUsing(fn ($state) => number_format($state, 2).' '.$currency),
                                TextEntry::make('paymentType.name'),
                                TextEntry::make('reference_number'),
                            ])
                            ->columns(3),
                        TextEntry::make('notes'),
                    ]),

            ]);
    }

    /**
     * @return string[]
     */
    public function getRelationManagers(): array
    {
        return [
            SaleResource\RelationManagers\SaleItemsRelationManager::class,
        ];
    }
}
