<?php

namespace App\Models;

use Carbon\Carbon;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoodsReceipt extends Model
{
    use HasFactory;

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
        // get all records that are generated today
        $code = (self::whereDate('created_at', Carbon::today())->max('id') ?? 0) + 1;
        $code = str_pad($code, 5, '0', STR_PAD_LEFT);

        $date = now()->format('Ymd');

        // PO-2024010100001
        return "GRN-{$date}{$code}";
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
        return number_format($this->getTotalCostAttribute(), 2).' '.Filament::getTenant()->getCurrency();
    }

    /**
     * @return string
     */
    public function getFormattedUnitCostAttribute(): string
    {
        return number_format($this->unit_cost, 2).' '.Filament::getTenant()->getCurrency();
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
