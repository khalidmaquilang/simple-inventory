<?php

namespace App\Listeners;

use App\Events\GoodsReceiptCreated;
use App\Models\Inventory;
use App\Models\StockMovement;

class CreateStockMovement
{
    /**
     * Handle the event.
     */
    public function handle(GoodsReceiptCreated $event): void
    {
        $productId = $event->productId;
        $userId = $event->userId;
        $supplierId = $event->supplierId;
        $customerId = $event->customerId;
        $referenceNumber = $event->referenceNumber;
        $quantity = $event->quantity;
        $unitCost = $event->unitCost;
        $type = $event->type;

        $inventory = Inventory::where('product_id', $productId)->first();
        if (empty($inventory)) {
            $inventory = $this->createInventory($productId, $userId);
        }

        StockMovement::create([
            'inventory_id' => $inventory->id,
            'user_id' => $userId,
            'supplier_id' => $supplierId,
            'customer_id' => $customerId,
            'reference_number' => $referenceNumber,
            'quantity_before_adjustment' => $inventory->quantity_on_hand,
            'quantity' => $quantity,
            'type' => $type,
        ]);

        // update average cost
        $inventory->updateAverageCost($quantity, $unitCost);

        // update inventory onhand and average cost
        $inventory->update([
            'quantity_on_hand' => $inventory->quantity_on_hand + $quantity,
        ]);
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