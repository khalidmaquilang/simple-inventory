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
    protected $fillable = [
        'company_id',
        'user_id',
        'unit_id',
        'product_id',
        'quantity_on_hand',
        'average_cost',
    ];

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
        return number_format($this->average_cost, 2).' '.$this->company->getCurrency();
    }

    /**
     * @return string
     */
    public function getQuantityUnit(): string
    {
        return "{$this->quantity_on_hand} {$this->unit->abbreviation}";
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

    /**
     * @return BelongsTo
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
