<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Filament\Resources\SupplierResource;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewSupplier extends ViewRecord
{
    protected static string $resource = SupplierResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Supplier Information')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('company_name'),
                        TextEntry::make('contact_person'),
                        TextEntry::make('email'),
                        TextEntry::make('phone'),
                    ]),
            ]);
    }
}
