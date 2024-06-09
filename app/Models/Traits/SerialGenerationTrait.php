<?php

namespace App\Models\Traits;

use Carbon\Carbon;
use Filament\Facades\Filament;

trait SerialGenerationTrait
{
    /**
     * @param  string  $identifier
     * @return string
     */
    public static function generateCodeByIdentifier(string $identifier): string
    {
        // get all records that are generated today
        $company = Filament::getTenant();
        $code = (self::whereDate('created_at', Carbon::today())->max('id') ?? 0) + 1;
        $code = str_pad($code, 5, '0', STR_PAD_LEFT);

        $date = now()->format('Ymd');

        // Identifier-202401010000101
        return "{$identifier}-{$date}{$code}{$company->id}";
    }
}