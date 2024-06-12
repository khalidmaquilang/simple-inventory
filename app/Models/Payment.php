<?php

namespace App\Models;

use App\Enums\PaymentStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * @var \class-string[]
     */
    protected $casts = [
        'payment_status' => PaymentStatusEnum::class,
    ];
}
