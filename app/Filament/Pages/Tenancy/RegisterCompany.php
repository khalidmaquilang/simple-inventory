<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Company;
use App\Models\Currency;
use App\Models\Role;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Pages\Tenancy\RegisterTenant;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class RegisterCompany extends RegisterTenant
{
    /**
     * @return string
     */
    public static function getLabel(): string
    {
        return 'Register team';
    }

    /**
     * @param  Form  $form
     * @return Form
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->lazy()
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
                    ->required(),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('phone')
                    ->tel()
                    ->required(),
                TextInput::make('email')
                    ->email()
                    ->required(),
                Textarea::make('address')
                    ->required(),
                FileUpload::make('company_logo')
                    ->image()
                    ->maxSize(2048),
                Select::make('currency')
                    ->options(Currency::getCurrencyList())
                    ->required(),
            ]);
    }

    /**
     * @param  array  $data
     * @return Company
     */
    protected function handleRegistration(array $data): Company
    {
        $user = auth()->user();
        $company = Company::create(array_merge($data, ['user_id', $user->id]));

        $company->members()->attach($user);

        $this->createRoles($company, $user);

        return $company;
    }

    /**
     * @param  Company  $company
     * @param  $user
     * @return void
     */
    protected function createRoles(Company $company, $user)
    {
        session(['company_id' => $company->id]);
        setPermissionsTeamId($company->id);

        $role = Role::create([
            'name' => config('filament-shield.super_admin.name'),
            'company_id' => $company->id,
        ]);
        $permissions = Permission::all();
        $role->syncPermissions($permissions);

        $user->assignRole($role);
    }
}
