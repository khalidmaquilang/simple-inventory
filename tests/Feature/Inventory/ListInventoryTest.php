<?php

namespace Tests\Feature\Inventory;

use App\Filament\Resources\InventoryResource;
use App\Models\Inventory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ListInventoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_can_access_list_inventory(): void
    {
        $this->login(['view_any_inventory']);

        $this->get(InventoryResource::getUrl())
            ->assertSuccessful();
    }

    /**
     * @return void
     */
    public function test_cant_access_list_inventory_with_wrong_permission(): void
    {
        $this->login(['list_something']);

        $this->get(InventoryResource::getUrl())
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function test_can_see_lists_of_inventories(): void
    {
        $this->login(['view_any_inventory']);
        $inventories = Inventory::factory(10)->create();

        Livewire::test(InventoryResource\Pages\ListInventories::class)
            ->assertCanSeeTableRecords($inventories);
    }
}
