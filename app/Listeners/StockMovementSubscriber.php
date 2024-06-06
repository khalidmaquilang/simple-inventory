<?php

namespace App\Listeners;

use App\Enums\StockMovementEnum;
use App\Events\GoodsReceiptCreated;
use App\Events\SaleCreated;
use App\Models\Inventory;
use App\Models\StockMovement;
use Illuminate\Events\Dispatcher;

class StockMovementSubscriber
{
    /**
     * @param  $event
     * @return void
     */
    public function handleStockMovement($event): void
    {
        \Log::debug('wew');
        $inventory = Inventory::where('product_id', $event->productId)->first();
        if (empty($inventory)) {
            $inventory = $this->createInventory($event->productId, $event->userId);
        }

        $quantity = ($event instanceof SaleCreated) ? -$event->quantity : $event->quantity;

        StockMovement::create([
            'inventory_id' => $inventory->id,
            'user_id' => $event->userId,
            'supplier_id' => $event->supplierId ?? null,
            'customer_id' => $event->customerId ?? null,
            'reference_number' => $event->referenceNumber,
            'quantity_before_adjustment' => $inventory->quantity_on_hand,
            'quantity' => $quantity,
            'type' => $event instanceof SaleCreated ? StockMovementEnum::SALE : StockMovementEnum::PURCHASE,
        ]);

        if ($event instanceof GoodsReceiptCreated) {
            $inventory->updateAverageCost($quantity, $event->unitCost);
        }

        $inventory->update([
            'quantity_on_hand' => $inventory->quantity_on_hand + $quantity,
        ]);
    }

    /**
     * @param  Dispatcher  $events
     * @return void
     */
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(GoodsReceiptCreated::class, [StockMovementSubscriber::class, 'handleStockMovement']);
        $events->listen(SaleCreated::class, [StockMovementSubscriber::class, 'handleStockMovement']);
    }

    /**
     * @param  string  $productId
     * @param  string  $userId
     * @return Inventory
     */
    protected function createInventory(string $productId, string $userId): Inventory
    {
        return Inventory::create([
            'user_id' => $userId,
            'product_id' => $productId,
            'quantity_on_hand' => 0,
            'average_cost' => 0,
        ]);
    }
}
