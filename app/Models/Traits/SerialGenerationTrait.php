<?php

namespace App\Models\Traits;

use App\Models\Company;
use Carbon\Carbon;
use Filament\Facades\Filament;

trait SerialGenerationTrait
{
    /**
     * @param  string  $identifier
     * @param  Company|null  $company
     * @return string
     */
    public static function generateCodeByIdentifier(string $identifier, ?Company $company = null): string
    {
        // get all records that are generated today
        $company = Filament::getTenant() ?? $company;
        $code = (self::whereDate('created_at', Carbon::today())->max('id') ?? 0) + 1;
        $code = str_pad($code, 5, '0', STR_PAD_LEFT);

        $date = now()->format('Ymd');

        // Identifier-12024010100001
        return "{$identifier}-{$company->id}{$date}{$code}";
    }
}
