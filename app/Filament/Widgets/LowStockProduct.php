<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ProductResource;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockProduct extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ProductResource::getEloquentQuery()->whereHas('inventory', function ($query) {
                    $query->whereColumn('quantity_on_hand', '<', 'reorder_point');
                })
            )
            ->heading('Low On Stock Products')
            ->defaultPaginationPageOption(5)
            ->columns([
                TextColumn::make('sku'),
                TextColumn::make('name'),
                TextColumn::make('inventory.quantity_on_hand'),
            ]);
    }
}
