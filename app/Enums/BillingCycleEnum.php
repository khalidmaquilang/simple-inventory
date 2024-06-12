<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum BillingCycleEnum: string implements HasLabel
{
    case MONTHLY = 'monthly';
    case YEARLY = 'yearly';

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::MONTHLY => 'Monthly',
            self::YEARLY => 'Yearly',
        };
    }
}
