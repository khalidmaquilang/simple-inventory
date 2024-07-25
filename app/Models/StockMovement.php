<?php

namespace App\Models;

use App\Enums\StockMovementEnum;
use App\Models\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory, TenantTrait;

    /**
     * @var string[]
     */
    protected $fillable = [
        'company_id',
        'user_id',
        'unit_id',
        'inventory_id',
        'customer_id',
        'supplier_id',
        'reference_number',
        'quantity_before_adjustment',
        'quantity',
        'quantity_base_unit',
        'type',
        'note',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'type' => StockMovementEnum::class,
    ];

    /**
     * @return string
     */
    public function getQuantityUnit(): string
    {
        return "{$this->quantity} {$this->unit->abbreviation}";
    }

    /**
     * @return string
     */
    public function getQuantityBaseUnit(): string
    {
        return "{$this->quantity_base_unit} {$this->inventory->product->unit->abbreviation}";
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
    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    /**
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
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
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
