<?php

namespace App\Filament\Resources\PurchaseOrderResource\RelationManagers;

use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GoodsReceiptsRelationManager extends RelationManager
{
    protected static string $relationship = 'goodsReceipts';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('received_date')
                    ->required(),
                Forms\Components\Hidden::make('sku'),
                Forms\Components\Hidden::make('name'),
                Forms\Components\Hidden::make('unit_cost'),
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name',
                        modifyQueryUsing: function (Builder $query) {
                            // Get all purchase order items
                            $product_ids = $this->getOwnerRecord()->purchaseOrderItems->pluck('product_id')->toArray();
                            $query->whereIn('id', $product_ids);
                        })
                    ->afterStateUpdated(function ($set, $state) {
                        $product = Product::find($state);
                        if (empty($product)) {
                            $set('sku', '');
                            $set('name', '');
                            $set('unit_cost', '');
                            return;
                        }

                        $set('sku', $product->sku);
                        $set('name', $product->name);
                        $set('unit_cost', $product->purchase_price);
                    })
                    ->required(),
                Forms\Components\TextInput::make('quantity')
                    ->numeric()
                    ->required(),
                Forms\Components\Textarea::make('notes'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('grn_code')
            ->columns([
                Tables\Columns\TextColumn::make('grn_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('product.name'),
                Tables\Columns\TextColumn::make('quantity'),
                Tables\Columns\TextColumn::make('formatted_unit_cost')
                    ->label('Unit cost'),
                Tables\Columns\TextColumn::make('formatted_total_cost')
                    ->label('Total cost'),
                Tables\Columns\TextColumn::make('received_date')
                    ->date(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Created by'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(fn ($livewire) => $livewire->dispatch('refresh'))
                    ->visible(fn () => $this->getOwnerRecord()->isAvailable()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    /**
     * @return bool
     */
    public function isReadOnly(): bool
    {
        return false;
    }
}
