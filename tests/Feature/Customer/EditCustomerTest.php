<?php

namespace Tests\Feature\Customer;

use App\Filament\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EditCustomerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_can_access_edit_customer(): void
    {
        $this->login(['update_customer', 'view_any_customer']);

        $this->get(CustomerResource::getUrl('edit', [
            'record' => Customer::factory()->create(),
        ]))
            ->assertSuccessful();
    }

    /**
     * @return void
     */
    public function test_cant_access_edit_customer_with_wrong_permission(): void
    {
        $this->login(['edit_something']);

        $this->get(CustomerResource::getUrl('edit', [
            'record' => Customer::factory()->create(),
        ]))
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function test_can_edit_customer(): void
    {
        $user = $this->login(['update_customer', 'view_any_customer']);

        $newCustomer = Customer::factory()->make(['user_id' => $user->id])->toArray();

        Livewire::test(CustomerResource\Pages\EditCustomer::class, [
            'record' => Customer::factory()->create()->getRouteKey(),
        ])
            ->fillForm($newCustomer)
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('customers', $newCustomer);
    }

    /**
     * @return void
     */
    public function test_cant_edit_customer_with_wrong_permission(): void
    {
        $this->login(['edit_something']);

        Livewire::test(CustomerResource\Pages\EditCustomer::class, [
            'record' => Customer::factory()->create()->getRouteKey(),
        ])
            ->assertForbidden();
    }
}
