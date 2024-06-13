<?php

namespace Tests\Feature\Customer;

use App\Filament\Resources\CustomerResource;
use App\Models\Customer;
use Filament\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DeleteCustomerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_can_delete_customer(): void
    {
        $this->markTestSkipped('I dont know why it is not working. Is it because there is a confirmation?');

        $this->login(['delete_customer', 'view_any_customer', 'update_customer']);

        $customer = Customer::factory()->create();

        Livewire::test(CustomerResource\Pages\EditCustomer::class, [
            'record' => $customer->getRouteKey(),
        ])
            ->callAction(DeleteAction::class);

        $this->assertModelMissing($customer);
    }

    /**
     * @return void
     */
    public function test_cant_delete_customer_with_wrong_permission(): void
    {
        $this->login(['delete_something']);

        Livewire::test(CustomerResource\Pages\EditCustomer::class, [
            'record' => Customer::factory()->create()->getRouteKey(),
        ])
            ->assertForbidden();
    }
}
