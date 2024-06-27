<?php

namespace App\Models;

use App\Enums\PurchaseOrderEnum;
use App\Models\Traits\SerialGenerationTrait;
use App\Models\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use HasFactory, SerialGenerationTrait, SoftDeletes, TenantTrait;

    /**
     * @var string[]
     */
    protected $fillable = [
        'company_id',
        'purchase_code',
        'order_date',
        'expected_delivery_date',
        'status',
        'shipping_fee',
        'reference_number',
        'total_amount',
        'paid_amount',
        'supplier_id',
        'payment_type_id',
        'user_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'supplier_id' => 'integer',
        'payment_type_id' => 'integer',
        'user_id' => 'integer',
        'status' => PurchaseOrderEnum::class,
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'formatted_remaining_amount',
    ];

    /**
     * @return string
     */
    public function getFormattedRemainingAmountAttribute(): string
    {
        return number_format($this->remaining_amount, 2).' '.$this->company->getCurrency();
    }

    /**
     * @return string
     */
    public static function generateCode(): string
    {
        return self::generateCodeByIdentifier('PO');
    }

    /**
     * @return void
     */
    public function setCompleted(): void
    {
        $this->status = PurchaseOrderEnum::RECEIVED;
        $this->save();
    }

    /**
     * @return void
     */
    public function setCancelled(): void
    {
        $this->status = PurchaseOrderEnum::CANCELLED;
        $this->save();
    }

    /**
     * @return bool
     */
    public function isAvailable()
    {
        return $this->status === PurchaseOrderEnum::PENDING || $this->status === PurchaseOrderEnum::PARTIALLY_RECEIVED;
    }

    /**
     * @return BelongsTo
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * @return BelongsTo
     */
    public function paymentType(): BelongsTo
    {
        return $this->belongsTo(PaymentType::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany
     */
    public function purchaseOrderItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    /**
     * @return HasMany
     */
    public function goodsReceipts(): HasMany
    {
        return $this->hasMany(GoodsReceipt::class);
    }
}
