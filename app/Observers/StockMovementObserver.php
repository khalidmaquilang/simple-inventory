<?php

namespace App\Observers;

use App\Models\StockMovement;

class StockMovementObserver extends BaseObserver
{
    /**
     * @param  StockMovement  $stockMovement
     * @return void
     */
    public function created(StockMovement $stockMovement): void
    {
        $stockMovement->quantity_base_unit = $stockMovement->unit->isBaseUnit()
            ? $stockMovement->quantity
            : $stockMovement->unit->convertFromBase($stockMovement->quantity);

        $stockMovement->save();
    }
}
