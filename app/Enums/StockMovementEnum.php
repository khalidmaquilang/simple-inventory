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
    case TRANSFER = 'transfer';
    case WRITE_OFF = 'write_off';
    case RTO = 'return_to_supplier';

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::PURCHASE => 'Purchase',
            self::SALE => 'Sale',
            self::ADJUSTMENT => 'Adjustment',
            self::RETURN => 'Customer Return',
            self::TRANSFER => 'Transfer',
            self::WRITE_OFF => 'Write Off',
            self::RTO => 'Return To Supplier',
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
            self::TRANSFER => 'info',
            self::WRITE_OFF => 'gray',
            self::RTO => 'danger',
        };
    }
}
