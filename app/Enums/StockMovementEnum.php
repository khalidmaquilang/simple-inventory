<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StockMovementEnum: string implements HasColor, HasLabel
{
    case PURCHASE = 'purchase';
    case SALE = 'sale';
    case ADJUSTMENT = 'adjustment';
    case RETURN = 'return';

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::PURCHASE => 'Purchase',
            self::SALE => 'Sale',
            self::ADJUSTMENT => 'Adjustment',
            self::RETURN => 'Return',
        };
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return match ($this) {
            self::PURCHASE => 'info',
            self::SALE => 'success',
            self::ADJUSTMENT => 'warning',
            self::RETURN => 'danger',
        };
    }
}
