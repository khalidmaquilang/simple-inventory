<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PlanTypeEnum: string implements HasLabel
{
    case STANDARD = 'standard';
    case CUSTOM = 'custom';

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::STANDARD => 'Standard',
            self::CUSTOM => 'Custom',
        };
    }
}
