<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $fillable = [
        'company_id',
        'sku',
        'name',
        'quantity',
        'unit_cost',
        'sale_id',
        'product_id',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'formatted_unit_cost',
        'formatted_total_cost',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'sale_id' => 'integer',
        'product_id' => 'integer',
    ];

    /**
     * @return string
     */
    public function getFormattedUnitCostAttribute(): string
    {
        return number_format($this->unit_cost, 2).' '.$this->company->getCurrency();
    }

    /**
     * @return string
     */
    public function getFormattedTotalCostAttribute(): string
    {
        return number_format($this->unit_cost * $this->quantity, 2).' '.$this->company->getCurrency();
    }

    /**
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return BelongsTo
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
