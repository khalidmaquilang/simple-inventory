<?php

namespace App\Models;

use App\Enums\PaymentStatusEnum;
use App\Models\Traits\SerialGenerationTrait;
use App\Models\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory, SerialGenerationTrait, TenantTrait;

    protected $fillable = [
        'company_id',
        'invoice_number',
        'subscription_id',
        'amount',
        'payment_date',
        'status',
        'payment_method',
        'reference_number',
    ];

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
