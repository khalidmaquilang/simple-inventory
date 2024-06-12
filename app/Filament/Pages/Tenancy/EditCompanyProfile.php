<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Company;
use Filament\Forms\Form;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\Tenancy\EditTenantProfile;
use Illuminate\Database\Eloquent\Model;

class EditCompanyProfile extends EditTenantProfile
{
    protected static string $view = 'filament.pages.edit-company-profile';

    public static function getLabel(): string
    {
        return 'Company Profile';
    }

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
                            Action::make('changeSubscriptionPlan'),
                        ]),
                    ]),
            ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(Company::getForm());
    }

    /**
     * Only the one who created the company can edit
     *
     * @param  Model  $tenant
     * @return bool
     */
    public static function canView(Model $tenant): bool
    {
        return $tenant->user_id === auth()->id();
    }
}
