<?php

namespace Tests\Feature\Customer;

use App\Filament\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ListCustomerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_can_access_list_customer(): void
    {
        $this->login(['view_any_customer']);

        $this->get(CustomerResource::getUrl())
            ->assertSuccessful();
    }

    /**
     * @return void
     */
    public function test_cant_access_list_customer_with_wrong_permission(): void
    {
        $this->login(['list_something']);

        $this->get(CustomerResource::getUrl())
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function test_can_see_lists_of_customers(): void
    {
        $this->login(['view_any_customer']);
        $customers = Customer::factory(10)->create();

        Livewire::test(CustomerResource\Pages\ListCustomers::class)
            ->assertCanSeeTableRecords($customers);
    }
}
