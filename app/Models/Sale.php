<?php

namespace App\Models;

use App\Enums\DiscountTypeEnum;
use App\Models\Traits\SerialGenerationTrait;
use App\Models\Traits\TenantTrait;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SerialGenerationTrait, SoftDeletes, TenantTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'company_id',
        'invoice_number',
        'sale_date',
        'vat',
        'shipping_fee',
        'discount',
        'discount_type',
        'total_amount',
        'paid_amount',
        'pay_until',
        'notes',
        'customer_id',
        'payment_type_id',
        'reference_number',
        'user_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'sale_date' => 'date',
        'vat' => 'double',
        'customer_id' => 'integer',
        'payment_type_id' => 'integer',
        'user_id' => 'integer',
        'discount_type' => DiscountTypeEnum::class,
    ];

    /**
     * @var string[]
     */
    protected $with = [
        'saleItems',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'remaining_amount',
        'formatted_remaining_amount',
        'formatted_discount',
    ];

    /**
     * @return array
     */
    public static function getPayDueAmountForm(): array
    {
        return [
            Group::make([
                TextInput::make('paid_amount')
                    ->hint(function ($record) {
                        return 'You need to pay '.$record->formatted_remaining_amount;
                    })
                    ->minValue(1)
                    ->maxValue(function ($record): float {
                        return $record->remaining_amount;
                    })
                    ->numeric()
                    ->required()
                    ->hintAction(
                        Action::make('pay_in_full')
                            ->icon('heroicon-m-arrow-down-tray')
                            ->action(function (Set $set, $state, $record) {
                                $set('paid_amount', $record->remaining_amount);
                            })
                    ),
                TextInput::make('reference_number'),
            ]),
        ];
    }

    /**
     * @return float
     */
    public function getRemainingAmountAttribute(): float
    {
        return $this->total_amount - $this->paid_amount;
    }

    /**
     * @return string
     */
    public function getFormattedRemainingAmountAttribute(): string
    {
        return number_format($this->getRemainingAmountAttribute(), 2).' '.$this->company->getCurrency();
    }

    /**
     * @return string
     */
    public function getFormattedDiscountAttribute(): string
    {
        if ($this->discount_type === DiscountTypeEnum::FIXED) {
            return number_format($this->discount, 2).' '.$this->company->getCurrency();
        }

        return $this->discount.'%';
    }

    /**
     * @return string
     */
    public static function generateCode(): string
    {
        return self::generateCodeByIdentifier('INV');
    }

    /**
     * @return string
     */
    public function getSubTotal(): string
    {
        return number_format($this->saleItems->sum(function ($item) {
            return $item->quantity * $item->unit_cost;
        }), 2);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function paymentType(): BelongsTo
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }
}
