<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Company;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile;
use Illuminate\Database\Eloquent\Model;

class EditCompanyProfile extends EditTenantProfile
{
    protected static string $view = 'filament.pages.edit-company-profile';

    public static function getLabel(): string
    {
        return 'Company Profile';
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
