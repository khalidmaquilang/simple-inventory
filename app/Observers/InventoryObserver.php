<?php

namespace App\Observers;

use App\Models\Inventory;
use App\Notifications\StockLowAlert;

class InventoryObserver extends BaseObserver
{
    public function updated(Inventory $inventory)
    {
        $product = $inventory->product;

        if (
            $inventory->wasChanged('quantity_on_hand') &&
            $inventory->quantity_on_hand < $product->reorder_point &&
            ($product->last_notified_at === null || $product->last_notified_at->addDay() <= now())
        ) {
            $company = filament()->getTenant();
            $users = $company->members;

            $users->each->notify(new StockLowAlert($inventory));

            $product->update(['last_notified_at' => now()]);
        }
    }
}
