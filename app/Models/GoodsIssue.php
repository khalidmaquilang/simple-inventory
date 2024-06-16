<?php

namespace App\Models;

use App\Enums\GoodsIssueTypeEnum;
use App\Models\Traits\SerialGenerationTrait;
use App\Models\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoodsIssue extends Model
{
    use HasFactory, SerialGenerationTrait, TenantTrait;

    protected $casts = [
        'type' => GoodsIssueTypeEnum::class,
    ];

    /**
     * @return string
     */
    public static function generateCode(): string
    {
        return self::generateCodeByIdentifier('GIN');
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
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
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
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
