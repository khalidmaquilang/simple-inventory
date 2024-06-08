<?php

namespace App\Models;

use App\Models\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    use HasFactory, TenantTrait;

    /**
     * @var string[]
     */
    protected $appends = [
        'formatted_average_cost',
    ];

    /**
     * @return string
     */
    public function getFormattedAverageCostAttribute(): string
    {
        return number_format($this->average_cost, 2).' '.Setting::getCurrency();
    }

    /**
     * @param  int  $quantity
     * @param  float  $unitCost
     * @return void
     */
    public function updateAverageCost(int $quantity, float $unitCost): void
    {
        $this->average_cost = (($this->quantity_on_hand * $this->average_cost) + ($quantity * $unitCost)) / ($quantity + $this->quantity_on_hand);
        $this->save();
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return HasMany
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }
}
