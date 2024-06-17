<?php

namespace Tests\Feature\Inventory;

use App\Enums\StockMovementEnum;
use App\Filament\Resources\InventoryResource\Pages\ViewInventory;
use App\Filament\Resources\InventoryResource\RelationManagers\StockMovementsRelationManager;
use App\Models\Inventory;
use App\Models\Product;
use App\Notifications\StockLowAlert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class AlertStockTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_send_alert_when_product_is_too_low()
    {
        Notification::fake();

        $user = $this->login();

        $product = Product::factory()->create([
            'reorder_point' => 5,
            'last_notified_at' => null,
        ]);

        $inventory = Inventory::factory()->create([
            'product_id' => $product->id,
            'quantity_on_hand' => 10,
        ]);

        Livewire::test(StockMovementsRelationManager::class, [
            'ownerRecord' => $inventory,
            'pageClass' => ViewInventory::class,
        ])
            ->callTableAction('create', null, [
                'reference_number' => 'INV-11111',
                'quantity' => -6,
                'type' => StockMovementEnum::SALE,
            ])
            ->assertHasNoTableActionErrors();

        Notification::assertSentTo([$user], StockLowAlert::class);
    }

    public function test_can_send_alert_once_a_day_when_product_is_too_low()
    {
        Notification::fake();

        $user = $this->login();

        $product = Product::factory()->create([
            'reorder_point' => 5,
            'last_notified_at' => null,
        ]);

        $inventory = Inventory::factory()->create([
            'product_id' => $product->id,
            'quantity_on_hand' => 10,
        ]);

        Livewire::test(StockMovementsRelationManager::class, [
            'ownerRecord' => $inventory,
            'pageClass' => ViewInventory::class,
        ])
            ->callTableAction('create', null, [
                'reference_number' => 'INV-11111',
                'quantity' => -6,
                'type' => StockMovementEnum::SALE,
            ]);

        Notification::assertSentTo([$user], StockLowAlert::class);

        Livewire::test(StockMovementsRelationManager::class, [
            'ownerRecord' => $inventory,
            'pageClass' => ViewInventory::class,
        ])
            ->callTableAction('create', null, [
                'reference_number' => 'INV-11111',
                'quantity' => -1,
                'type' => StockMovementEnum::SALE,
            ]);

        Notification::assertSentTimes(StockLowAlert::class, 1);
    }
}
