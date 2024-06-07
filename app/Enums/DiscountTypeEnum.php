<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum DiscountTypeEnum: string implements HasLabel
{
    case FIXED = 'fixed';
    case PERCENTAGE = 'percentage';

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::FIXED => 'Fixed',
            self::PERCENTAGE => 'Percentage',
        };
    }

    /**
     * @return array
     */
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
