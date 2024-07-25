<?php

namespace App\Models;

use App\Models\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    use HasFactory, TenantTrait;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'abbreviation',
        'unit_id',
        'conversion_factor',
    ];

    /**
     * @return BelongsTo
     */
    public function baseUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * @return HasMany
     */
    public function derivedUnits(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    /**
     * @return bool
     */
    public function isBaseUnit(): bool
    {
        return empty($this->unit_id);
    }

    /**
     * @param $quantity
     * @return float|int
     */
    public function convertToBase($quantity): float|int
    {
        return $quantity / ($this->conversion_factor ?: 1); // Avoid division by zero
    }

    /**
     * @param $quantity
     * @return float|int
     */
    public function convertFromBase($quantity): float|int
    {
        return $quantity * ($this->conversion_factor ?: 1);
    }
}
