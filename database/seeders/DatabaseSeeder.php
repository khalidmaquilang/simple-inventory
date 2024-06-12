<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\Traits\DisableForeignKeys;
use Database\Seeders\Traits\TruncateTable;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    use DisableForeignKeys, TruncateTable;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->disableForeignKeys();

        $this->truncateMultiple([
            config('permission.table_names.model_has_permissions'),
            config('permission.table_names.model_has_roles'),
            config('permission.table_names.role_has_permissions'),
            config('permission.table_names.permissions'),
            config('permission.table_names.roles'),
            'users',
            'password_reset_tokens',
            'customers',
            'suppliers',
            'categories',
            'payment_types',
            'purchase_orders',
            'purchase_order_items',
            'sales',
            'sale_items',
            'inventories',
            'stock_movements',
            'goods_receipts',
            'notifications',
            'imports',
            'exports',
            'failed_import_rows',
            'companies',
            'invites',
        ]);

        $this->enableForeignKeys();
    }
}
