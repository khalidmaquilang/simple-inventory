<?php

namespace App\Models;

use App\Enums\PurchaseOrderEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes;

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
     * @return string
     */
    public static function generateCode(): string
    {
        // get all records that are generated today
        $code = (self::whereDate('created_at', Carbon::today())->max('id') ?? 0) + 1;
        $code = str_pad($code, 5, '0', STR_PAD_LEFT);

        $date = now()->format('Ymd');

        // PO-2024010100001
        return "PO-{$date}{$code}";
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
