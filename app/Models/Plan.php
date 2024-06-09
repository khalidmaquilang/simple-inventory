<?php

namespace App\Models;

use App\Enums\BillingCycleEnum;
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
    ];
}
