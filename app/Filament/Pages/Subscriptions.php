<?php

namespace App\Filament\Pages;

use App\Models\Plan;
use Filament\Pages\Page;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class Subscriptions extends Page implements HasTable
{
    use InteractsWithTable;

    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.subscriptions';

    public function table(Table $table): Table
    {
        return $table
            ->query(Plan::query()->standard())
            ->columns([
                Stack::make([
                    TextColumn::make('name')
                        ->size(TextColumn\TextColumnSize::Large)
                        ->weight(fn ($record) => $this->bold($record->price)),
                    TextColumn::make('price')
                        ->weight(fn ($record) => $this->bold($record->price))
                        ->money(fn () => filament()->getTenant()->getCurrency()),
                    TextColumn::make('billing_cycle')
                        ->weight(fn ($record) => $this->bold($record->price)),
                    TextColumn::make('features')
                        ->weight(fn ($record) => $this->bold($record->price))
                        ->listWithLineBreaks()
                        ->bulleted(),
                ]),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->actions([
                Action::make('Current Plan')
                    ->visible(fn ($record) => $record->id === filament()->getTenant()->getActiveSubscription()->plan_id),
                Action::make('Contact Us')
                    ->button()
                    ->action(fn () => redirect('https://www.facebook.com/stockmanageronline'))
                    ->visible(fn ($record) => $record->id !== filament()->getTenant()->getActiveSubscription()->plan_id),
            ]);
    }

    /**
     * @param  $price
     * @return FontWeight
     */
    protected function bold($price)
    {
        if ($price == 999) {
            return FontWeight::ExtraBold;
        }

        return FontWeight::Medium;
    }
}
