<?php

namespace App\Filament\Resources\InventoryResource\RelationManagers;

use App\Enums\GoodsIssueTypeEnum;
use App\Enums\PurchaseOrderEnum;
use App\Enums\StockMovementEnum;
use App\Models\Customer;
use App\Models\StockMovement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class StockMovementsRelationManager extends RelationManager
{
    protected static string $relationship = 'stockMovements';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make('Stock Movement')
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
                    ]),
                Forms\Components\Fieldset::make('From/To')
                    ->schema([
                        Forms\Components\Select::make('customer_id')
                            ->createOptionForm(Customer::getForm())
                            ->searchable()
                            ->hint('Optional')
                            ->relationship('customer', 'name')
                            ->nullable(),
                        Forms\Components\Select::make('supplier_id')
                            ->hint('Optional')
                            ->relationship('supplier', 'company_name')
                            ->nullable(),
                    ]),
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
                Tables\Columns\TextColumn::make('reference_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity_before_adjustment'),
                Tables\Columns\TextColumn::make('quantity'),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('supplier.company_name'),
                Tables\Columns\TextColumn::make('customer.name'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Created By'),
            ])
            ->filters([
                DateRangeFilter::make('created_at'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data) {
                        $data['quantity_before_adjustment'] = $this->getOwnerRecord()->quantity_on_hand;

                        return $data;
                    })
                    ->before(fn (Tables\Actions\CreateAction $action, array $data) => $this->before($action, $data))
                    ->after(
                        fn (StockMovement $stockMovement, $livewire) => $this->updateInventoryOnHand(
                            $stockMovement,
                            $livewire
                        )
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Stock Movements'),
            'purchase' => Tab::make('Purchase')
                ->modifyQueryUsing(fn ($query) => $query->where('type', StockMovementEnum::PURCHASE)),
            'sale' => Tab::make('Sale')
                ->modifyQueryUsing(fn ($query) => $query->where('type', StockMovementEnum::SALE)),
            'adjustment' => Tab::make('Adjustment')
                ->modifyQueryUsing(fn ($query) => $query->where('type', StockMovementEnum::ADJUSTMENT)),
            'return' => Tab::make('Return')
                ->modifyQueryUsing(fn ($query) => $query->where('type', StockMovementEnum::RETURN)),
        ];
    }

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

    /**
     * @param  Tables\Actions\CreateAction  $action
     * @param  $data
     * @return void
     *
     * @throws \Filament\Support\Exceptions\Halt
     */
    protected function before(Tables\Actions\CreateAction $action, $data)
    {
        $company = filament()->getTenant();

        if (PurchaseOrderEnum::tryFrom(
            $data['type']
        ) === StockMovementEnum::PURCHASE->value && $company->hasReachedMaxPurchaseOrders()) {
            $this->reachedLimitNotification($action);
        }

        if (PurchaseOrderEnum::tryFrom(
            $data['type']
        ) === StockMovementEnum::SALE->value && $company->hasReachedMaxSales()) {
            $this->reachedLimitNotification($action);
        }

        if (GoodsIssueTypeEnum::tryFrom($data['type']) && $company->hasReachedMaxGoodsIssues()) {
            $this->reachedLimitNotification($action);
        }
    }

    /**
     * @param  Tables\Actions\CreateAction  $action
     * @return void
     *
     * @throws \Filament\Support\Exceptions\Halt
     */
    protected function reachedLimitNotification(Tables\Actions\CreateAction $action): void
    {
        Notification::make()
            ->warning()
            ->title('Reached Limit!')
            ->body(
                "You've reached your Limit. Please contact us to discuss upgrading your plan for higher limits."
            )
            ->persistent()
            ->actions([
                Tables\Actions\Action::make('Upgrade')
                    ->button()
                    ->url(redirect('https://www.facebook.com/stockmanageronline'), shouldOpenInNewTab: true),
            ])
            ->send();

        $action->halt();
    }
}
