<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum SubscriptionStatusEnum: string implements HasColor, HasLabel
{
    case ACTIVE = 'active';
    case TRIAL = 'trialing';
    case CANCELED = 'canceled';
    case PAST_DUE = 'past_due';
    case UNPAID = 'unpaid';

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::TRIAL => 'Trialing',
            self::CANCELED => 'Canceled',
            self::PAST_DUE => 'Past Due',
            self::UNPAID => 'Unpaid',
        };
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::TRIAL => 'info',
            self::CANCELED => 'danger',
            self::PAST_DUE => 'warning',
            self::UNPAID => 'danger',
        };
    }
}
