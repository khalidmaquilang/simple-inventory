<?php

namespace App\Filament\Admin\Resources\SubscriptionResource\Pages;

use App\Filament\Admin\Resources\SubscriptionResource;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewSubscription extends ViewRecord
{
    protected static string $resource = SubscriptionResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->columns()
                    ->schema([
                        TextEntry::make('company.name'),
                        TextEntry::make('plan.name'),
                        TextEntry::make('start_date')
                            ->date(),
                        TextEntry::make('end_date')
                            ->date(),
                        TextEntry::make('status')
                            ->badge(),
                        TextEntry::make('extra_users'),
                        TextEntry::make('total_amount')
                            ->money('PHP'),
                    ]),
            ]);
    }

    public function getRelationManagers(): array
    {
        return [
            SubscriptionResource\RelationManagers\PaymentsRelationManager::class,
        ];
    }
}
