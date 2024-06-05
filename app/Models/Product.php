<?php

namespace App\Models;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];

    /**
     * @return array
     */
    public static function getForm(): array
    {
        return [
            TextInput::make('sku')
                ->label('SKU')
                ->unique(ignoreRecord: true)
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
            Textarea::make('description')
                ->columnSpanFull(),
        ];
    }
}
