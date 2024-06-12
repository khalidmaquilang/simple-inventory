<?php

namespace App\Models;

use App\Enums\BillingCycleEnum;
use App\Enums\PlanTypeEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'features' => 'array',
        'billing_cycle' => BillingCycleEnum::class,
        'type' => PlanTypeEnum::class,
    ];

    /**
     * @param  Builder  $query
     * @return void
     */
    public function scopeStandard(Builder $query): void
    {
        $query->where('type', PlanTypeEnum::STANDARD);
    }
}
