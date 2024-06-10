<?php

namespace Tests;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

abstract class TestCase extends BaseTestCase
{
    /**
     * @var Company
     */
    protected Company $company;

    /**
     * setup.
     */
    public function setUp(): void
    {
        parent::setUp();

        // reset db
        Artisan::call('db:seed');
    }

    /**
     * @param  string|array  $permissions
     * @return User
     */
    public function login(string|array $permissions): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->setupCompany($user);
        $this->setupPermission($permissions);

        $role = Role::create([
            'name' => Str::random(5),
            'company_id' => $this->company->id,
        ]);
        $role->syncPermissions($permissions);

        $user->assignRole($role);

        return $user;
    }

    /**
     * @param  string|array  $permissions
     * @return void
     */
    protected function setupPermission(string|array $permissions): void
    {
        if (is_string($permissions)) {
            Permission::create(['name' => $permissions]);

            return;
        }

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }

    /**
     * @param  User  $user
     * @return void
     */
    protected function setupCompany(User $user)
    {
        $this->company = Company::factory()->create([
            'user_id' => $user->id,
        ]);
        $this->company->members()->attach($user);

        setPermissionsTeamId($this->company->id);
        Filament::setTenant($this->company);
    }

    /**
     * @return Company
     */
    public function getCurrentCompany(): Company
    {
        return $this->company;
    }
}
