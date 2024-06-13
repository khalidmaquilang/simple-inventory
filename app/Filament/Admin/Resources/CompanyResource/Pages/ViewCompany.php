<?php

namespace App\Filament\Admin\Resources\CompanyResource\Pages;

use App\Filament\Admin\Resources\CompanyResource;
use Filament\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewCompany extends ViewRecord
{
    protected static string $resource = CompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('slug'),
                        TextEntry::make('owner.name'),
                        TextEntry::make('phone'),
                        TextEntry::make('email'),
                        TextEntry::make('currency'),
                    ]),
            ]);
    }

    /**
     * @return \class-string[]
     */
    public function getRelationManagers(): array
    {
        return [
            CompanyResource\RelationManagers\SubscriptionsRelationManager::class,
        ];
    }
}
