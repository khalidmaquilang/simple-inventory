<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Company;
use App\Models\Role;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant;
use Spatie\Permission\Models\Permission;

class RegisterCompany extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Register team';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required()
                    ->unique(),
            ]);
    }

    protected function handleRegistration(array $data): Company
    {
        $company = Company::create($data);

        $company->members()->attach(auth()->user());

        $this->createRoles($company, auth()->user());

        return $company;
    }

    protected function createRoles(Company $company, $user)
    {
        $role = Role::create([
            'name' => config('filament-shield.super_admin.name'),
            'company_id' => $company->id,
        ]);
        $permissions = Permission::all();
        $role->syncPermissions($permissions);

        session(['company_id' => $company->id]);
        setPermissionsTeamId($company->id);

        $user->assignRole($role);
    }
}
