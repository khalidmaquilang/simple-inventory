<?php

namespace App\Listeners;

use App\Enums\PurchaseOrderEnum;
use App\Enums\StockMovementEnum;
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
        $goodsReceipt = $event->goodsReceipt;

        $inventory = Inventory::where('product_id', $goodsReceipt->product_id)->first();
        if (empty($inventory)) {
            $inventory = $this->createInventory($goodsReceipt->product_id, $goodsReceipt->user_id);
        }

        StockMovement::create([
            'inventory_id' => $inventory->id,
            'user_id' => $goodsReceipt->user_id,
            'supplier_id' => $goodsReceipt->purchaseOrder->supplier_id,
            'reference_number' => $goodsReceipt->grn_code,
            'quantity_before_adjustment' => $inventory->quantity_on_hand,
            'quantity' => $goodsReceipt->quantity,
            'type' => StockMovementEnum::PURCHASE->value,
        ]);

        // update inventory onhand
        $inventory->update([
            'quantity_on_hand' => $inventory->quantity_on_hand + $goodsReceipt->quantity,
        ]);

        // update purchase order status
        $goodsReceipt->purchaseOrder->update([
            'status' => PurchaseOrderEnum::PARTIALLY_RECEIVED->value,
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
        ]);
    }
}
