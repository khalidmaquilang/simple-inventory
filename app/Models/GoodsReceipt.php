<?php

namespace App\Models;

use App\Models\Traits\SerialGenerationTrait;
use App\Models\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoodsReceipt extends Model
{
    use HasFactory, SerialGenerationTrait, TenantTrait;

    /**
     * @var string[]
     */
    protected $appends = [
        'total_cost',
        'formatted_total_cost',
        'formatted_unit_cost',
    ];

    /**
     * @return string
     */
    public static function generateCode(): string
    {
        return self::generateCodeByIdentifier('GRN');
    }

    /**
     * @return float
     */
    public function getTotalCostAttribute(): float
    {
        return $this->quantity * $this->unit_cost;
    }

    /**
     * @return string
     */
    public function getFormattedTotalCostAttribute(): string
    {
        return number_format($this->getTotalCostAttribute(), 2).' '.$this->company->getCurrency();
    }

    /**
     * @return string
     */
    public function getFormattedUnitCostAttribute(): string
    {
        return number_format($this->unit_cost, 2).' '.$this->company->getCurrency();
    }

    /**
     * @return BelongsTo
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
