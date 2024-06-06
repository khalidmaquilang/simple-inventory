<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PurchaseOrderEnum: string implements HasColor, HasLabel
{
    case PENDING = 'pending';
    case RECEIVED = 'received';
    case PARTIALLY_RECEIVED = 'partially_received';
    case CANCELLED = 'cancelled';

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::RECEIVED => 'Received',
            self::PARTIALLY_RECEIVED => 'Partially Received',
            self::CANCELLED => 'Cancelled',
        };
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'info',
            self::RECEIVED => 'success',
            self::PARTIALLY_RECEIVED => 'warning',
            self::CANCELLED => 'danger',
        };
    }
}
