<?php

namespace App\Models;

use App\Models\Traits\TenantTrait;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rules\Unique;

class Product extends Model
{
    use HasFactory, SoftDeletes, TenantTrait;

    protected $fillable = [
        'company_id',
        'category_id',
        'sku',
        'name',
        'purchase_price',
        'selling_price',
        'reorder_point',
        'last_notified_at',
        'description',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'current_stock',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'last_notified_at' => 'datetime',
    ];

    /**
     * @return array
     */
    public static function getForm(): array
    {
        return [
            Select::make('category_id')
                ->relationship('category', 'name')
                ->required(),
            TextInput::make('sku')
                ->label('SKU')
                ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule) {
                    return $rule->where('company_id', Filament::getTenant()->id);
                })
                ->validationMessages([
                    'unique' => 'The SKU has already been taken.',
                ])
                ->required()
                ->maxLength(255),
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            TextInput::make('purchase_price')
                ->required()
                ->numeric(),
            TextInput::make('selling_price')
                ->required()
                ->numeric(),
            TextInput::make('reorder_point')
                ->hint('Restock Level')
                ->numeric(),
            Textarea::make('description')
                ->columnSpanFull(),
        ];
    }

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return HasOne
     */
    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }

    /**
     * @return int
     */
    public function getCurrentStockAttribute(): int
    {
        return optional($this->inventory)->quantity_on_hand ?? 0;
    }
}
