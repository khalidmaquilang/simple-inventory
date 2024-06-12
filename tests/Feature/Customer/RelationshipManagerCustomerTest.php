<?php

namespace Tests\Feature\Customer;

use App\Filament\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RelationshipManagerCustomerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_can_see_customers_sales_relation_manager(): void
    {
        $this->markTestSkipped('Dont know how to check the relation manager with custom url');

        $user = $this->login(['view_any_customer', 'view_customer']);

        $customer = Customer::factory()
            ->has(Sale::factory()->count(5))
            ->create([
                'user_id' => $user,
            ]);

        Livewire::test(CustomerResource\RelationManagers\SalesRelationManager::class, [
            'ownerRecord' => $customer,
            'pageClass' => CustomerResource\Pages\ViewCustomer::class,
        ])->assertSuccessful();
    }
}
