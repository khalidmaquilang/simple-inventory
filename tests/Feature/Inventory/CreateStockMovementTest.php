<?php

namespace Tests\Feature\Inventory;

use App\Enums\StockMovementEnum;
use App\Filament\Resources\InventoryResource\Pages\ViewInventory;
use App\Filament\Resources\InventoryResource\RelationManagers\StockMovementsRelationManager;
use App\Models\Inventory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CreateStockMovementTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_stock_movements()
    {
        $this->login();

        $inventory = Inventory::factory()->create([
            'quantity_on_hand' => 0,
        ]);

        Livewire::test(StockMovementsRelationManager::class, [
            'ownerRecord' => $inventory,
            'pageClass' => ViewInventory::class,
        ])
            ->callTableAction('create', null, [
                'reference_number' => 'INV-11111',
                'quantity' => 1,
                'type' => StockMovementEnum::SALE,
            ])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('stock_movements', [
            'inventory_id' => $inventory->id,
            'reference_number' => 'INV-11111',
            'type' => StockMovementEnum::SALE->value,
            'quantity_before_adjustment' => 0,
            'quantity' => 1,
        ]);
    }
}
