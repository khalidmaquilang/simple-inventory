<?php

namespace App\Models;

use App\Enums\DiscountTypeEnum;
use App\Models\Traits\TenantTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SoftDeletes, TenantTrait;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'sale_date' => 'date',
        'vat' => 'double',
        'customer_id' => 'integer',
        'payment_type_id' => 'integer',
        'user_id' => 'integer',
        'discount_type' => DiscountTypeEnum::class,
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'remaining_amount',
        'formatted_remaining_amount',
    ];

    /**
     * @return float
     */
    public function getRemainingAmountAttribute(): float
    {
        return $this->total_amount - $this->paid_amount;
    }

    /**
     * @return string
     */
    public function getFormattedRemainingAmountAttribute(): string
    {
        return number_format($this->getRemainingAmountAttribute(), 2).' '.Setting::getCurrency();
    }

    /**
     * @return string
     */
    public static function generateCode(): string
    {
        // get all records that are generated today
        $code = (self::whereDate('created_at', Carbon::today())->max('id') ?? 0) + 1;
        $code = str_pad($code, 5, '0', STR_PAD_LEFT);

        $date = now()->format('Ymd');

        // INV-2024010100001
        return "INV-{$date}{$code}";
    }

    /**
     * @return string
     */
    public function getSubTotal(): string
    {
        return number_format($this->saleItems->sum(function ($item) {
            return $item->quantity * $item->unit_cost;
        }), 2);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function paymentType(): BelongsTo
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }
}
