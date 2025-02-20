<?php

namespace Tests\Feature\Customer;

use App\Filament\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CreateCustomerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_can_access_create_customer(): void
    {
        $this->login(['create_customer', 'view_any_customer']);

        $this->get(CustomerResource::getUrl('create'))
            ->assertSuccessful();
    }

    /**
     * @return void
     */
    public function test_cant_access_create_customer_with_wrong_permission(): void
    {
        $this->login(['create_something']);

        $this->get(CustomerResource::getUrl('create'))
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function test_can_create_customer(): void
    {
        $user = $this->login(['create_customer', 'view_any_customer']);

        $customer = Customer::factory()->make(['user_id' => $user->id])->toArray();

        Livewire::test(CustomerResource\Pages\CreateCustomer::class)
            ->fillForm($customer)
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('customers', $customer);
    }

    /**
     * @return void
     */
    public function test_cant_create_customer_with_wrong_permission(): void
    {
        $this->login(['create_something']);

        Livewire::test(CustomerResource\Pages\CreateCustomer::class)
            ->assertForbidden();
    }
}
