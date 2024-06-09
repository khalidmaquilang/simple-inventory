<?php

namespace App\Models;

use App\Models\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    use HasFactory, TenantTrait;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'purchase_order_id' => 'integer',
        'product_id' => 'integer',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'quantity_received',
    ];

    /**
     * @return int
     */
    public function getQuantityReceivedAttribute(): int
    {
        return $this->purchaseOrder
            ->goodsReceipts()
            ->where('product_id', $this->product_id)
            ->sum('quantity');
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
