<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum GoodsIssueTypeEnum: string implements HasColor, HasLabel
{
    case SALE = 'sale';
    case TRANSFER = 'transfer';
    case WRITE_OFF = 'write_off';
    case RTO = 'return_to_supplier';

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::SALE => 'Sale',
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
            self::SALE => 'success',
            self::TRANSFER => 'info',
            self::WRITE_OFF => 'gray',
            self::RTO => 'danger',
        };
    }
}
