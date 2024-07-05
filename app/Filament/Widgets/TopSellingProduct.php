<?php

namespace App\Filament\Widgets;

use App\Repositories\ProductRepository;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopSellingProduct extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 5;

    public function table(Table $table): Table
    {
        $repo = app(ProductRepository::class);

        return $table
            ->query(
                $repo->getTopSellingProducts()
            )
            ->heading('Top 10 Selling Products')
            ->defaultPaginationPageOption(10)
            ->paginated(false)
            ->columns([
                TextColumn::make('sku'),
                TextColumn::make('name'),
                TextColumn::make('total_quantity_sold')
                    ->numeric(),
                TextColumn::make('total_revenue')
                    ->money(filament()->getTenant()->getCurrency()),
            ]);
    }
}
