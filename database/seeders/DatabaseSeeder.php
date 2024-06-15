<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Enums\BillingCycleEnum;
use App\Enums\PlanTypeEnum;
use App\Models\Plan;
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
            'plans',
            'subscriptions',
            'payments',
            'goods_issues',
        ]);

        $plans = [
            [
                'name' => 'Freemium',
                'price' => 0,
                'billing_cycle' => BillingCycleEnum::MONTHLY,
                'features' => [
                    'Unlimited Customer',
                    'Unlimited Product',
                    'Unlimited Supplier',
                    '1 User',
                    '1 Role',
                    'Up to 10 Purchase Order/mo',
                    'Up to 10 Sale/mo',
                ],
                'max_users' => 1,
                'max_roles' => 1,
                'max_monthly_purchase_order' => 10,
                'max_monthly_sale_order' => 10,
            ],
            [
                'name' => 'Basic',
                'price' => 499,
                'billing_cycle' => BillingCycleEnum::MONTHLY,
                'features' => [
                    'Unlimited Customer',
                    'Unlimited Product',
                    'Unlimited Supplier',
                    '3 User',
                    '5 Role',
                    'Up to 30 Purchase Order/mo',
                    'Up to 30 Sale/mo',
                ],
                'max_users' => 3,
                'max_roles' => 5,
                'max_monthly_purchase_order' => 30,
                'max_monthly_sale_order' => 30,
            ],
            [
                'name' => 'Premium',
                'price' => 999,
                'billing_cycle' => BillingCycleEnum::MONTHLY,
                'features' => [
                    'Unlimited Customer',
                    'Unlimited Product',
                    'Unlimited Supplier',
                    '5 User',
                    'Unlimited Role',
                    'Unlimited Purchase Order',
                    'Unlimited Sale',
                ],
                'max_users' => 5,
                'max_roles' => 0,
                'max_monthly_purchase_order' => 0,
                'max_monthly_sale_order' => 0,
            ],
            [
                'name' => 'Super Admin',
                'price' => 0,
                'billing_cycle' => BillingCycleEnum::YEARLY,
                'features' => [
                    'Unlimited All',
                ],
                'max_users' => 0,
                'max_roles' => 0,
                'max_monthly_purchase_order' => 0,
                'max_monthly_sale_order' => 0,
                'type' => PlanTypeEnum::CUSTOM,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }

        $this->enableForeignKeys();
    }
}
