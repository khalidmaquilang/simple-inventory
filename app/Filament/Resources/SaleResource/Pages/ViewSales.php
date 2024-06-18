<?php

namespace App\Filament\Resources\SaleResource\Pages;

use App\Filament\Resources\SaleResource;
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
                            ->formatStateUsing(fn($state) => now()->addDays($state)->format('M d, Y')),
                        TextEntry::make('customer.name'),
                        Fieldset::make('Payment Information')
                            ->schema([
                                TextEntry::make('vat'),
                                TextEntry::make('formatted_discount')
                                    ->label('Discount'),
                                TextEntry::make('total_amount')
                                    ->formatStateUsing(fn($state) => number_format($state, 2).' '.$currency),
                                TextEntry::make('paid_amount')
                                    ->formatStateUsing(fn($state) => number_format($state, 2).' '.$currency),
                                TextEntry::make('paymentType.name'),
                                TextEntry::make('reference_number'),
                            ]),
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
