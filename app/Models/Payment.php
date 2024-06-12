<?php

namespace App\Models;

use App\Enums\PaymentStatusEnum;
use App\Models\Traits\SerialGenerationTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory, SerialGenerationTrait;

    /**
     * @var \class-string[]
     */
    protected $casts = [
        'payment_date' => 'date',
        'status' => PaymentStatusEnum::class,
    ];

    /**
     * @return string
     */
    public static function generateCode(): string
    {
        return self::generateCodeByIdentifier('INV', Company::find(1));
    }

    /**
     * @return BelongsTo
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
