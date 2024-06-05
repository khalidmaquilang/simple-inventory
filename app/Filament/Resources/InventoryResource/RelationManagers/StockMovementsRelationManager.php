<?php

namespace App\Filament\Resources\InventoryResource\RelationManagers;

use App\Enums\StockMovementEnum;
use App\Models\StockMovement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class StockMovementsRelationManager extends RelationManager
{
    protected static string $relationship = 'stockMovements';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('reference_number'),
                Forms\Components\TextInput::make('quantity')
                    ->hint('For outgoing products, use negative values.')
                    ->minValue(fn () => ($this->getOwnerRecord()->quantity_on_hand) * -1)
                    ->numeric()
                    ->required(),
                Forms\Components\Select::make('type')
                    ->options(StockMovementEnum::class)
                    ->required(),
                Forms\Components\Textarea::make('note'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reference_number'),
                Tables\Columns\TextColumn::make('quantity_before_adjustment'),
                Tables\Columns\TextColumn::make('quantity'),
                Tables\Columns\TextColumn::make('type')
                    ->badge(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Created By'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data) {
                        $data['quantity_before_adjustment'] = $this->getOwnerRecord()->quantity_on_hand;

                        return $data;
                    })
                    ->after(fn (StockMovement $stockMovement, $livewire) => $this->updateInventoryOnHand($stockMovement, $livewire)),
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

    /**
     * @param  StockMovement  $stockMovement
     * @return void
     */
    protected function updateInventoryOnHand(StockMovement $stockMovement, $livewire)
    {
        $inventory = $stockMovement->inventory;

        $inventory->update([
            'quantity_on_hand' => $inventory->quantity_on_hand + $stockMovement->quantity,
        ]);

        $livewire->dispatch('refresh');
    }
}