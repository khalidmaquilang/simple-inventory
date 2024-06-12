<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PaymentStatusEnum: string implements HasColor, HasLabel
{
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case PENDING = 'pending';

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::SUCCESS => 'Success',
            self::FAILED => 'Failed',
            self::PENDING => 'Pending',
        };
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return match ($this) {
            self::SUCCESS => 'success',
            self::FAILED => 'danger',
            self::PENDING => 'warning',
        };
    }
}
