<?php

namespace App\Console\Commands\Retrofits;

use App\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RetrofitSuperAdminPermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'retrofit:add-super-admin-permission {permissions* : permission names}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will add permissions on the super_admin roles.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $permissions = $this->argument('permissions');

        $this->line('Getting all Super Admin roles from different companies.');
        $roles = Role::where('name', config('filament-shield.super_admin.name'))
            ->where('company_id', '<>', null)
            ->get();

        $this->line($roles->count().' role/s found.');

        $this->line('Retrofitting...');
        $bar = $this->output->createProgressBar($roles->count());

        $errorCount = 0;
        $bar->start();

        foreach ($roles as $role) {
            try {
                $role->givePermissionTo($permissions);
            } catch (\Exception $exception) {
                Log::error('There was something wrong while retrofitting roles.', [
                    'payment_id' => $role->id,
                    'permissions' => $permissions,
                    'exception' => $exception,
                ]);
                $errorCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        if ($errorCount) {
            $this->error($errorCount.' errors found. Please check logs.');
        }

        $this->line('Retrofit Finished!');
    }
}
