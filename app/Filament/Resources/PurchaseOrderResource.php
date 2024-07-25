<?php

namespace App\Filament\Resources;

use App\Filament\Exports\PurchaseOrderExporter;
use App\Filament\Resources\PurchaseOrderResource\Pages;
use App\Filament\Resources\PurchaseOrderResource\Widgets\PurchaseOrderLimit;
use App\Models\Product;
use App\Models\PurchaseOrder;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Procurement';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        $currency = Filament::getTenant()->getCurrency();

        return $form
            ->schema([
                Forms\Components\DatePicker::make('order_date')
                    ->required(),
                Forms\Components\DatePicker::make('expected_delivery_date'),
                Forms\Components\Select::make('supplier_id')
                    ->relationship('supplier', 'company_name')
                    ->required(),
                TableRepeater::make('purchaseOrderItems')
                    ->relationship()
                    ->addActionLabel('Click to add more products')
                    ->headers([
                        Header::make('Product Name'),
                        Header::make('Quantity'),
                        Header::make('Unit Cost'),
                        Header::make('Total Cost'),
                    ])
                    ->schema([
                        Forms\Components\Hidden::make('sku'),
                        Forms\Components\Hidden::make('unit_cost'),
                        Forms\Components\Hidden::make('name'),
                        Forms\Components\Select::make('product_id')
                            ->relationship('product', 'sku_name_format')
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if (empty($state)) {
                                    $set('sku', '');
                                    $set('name', '');
                                    $set('quantity', '');
                                    $set('unit_cost', '');
                                    $set('formatted_unit_cost', '');

                                    return;
                                }

                                $product = Product::find($state);
                                $set('sku', $product->sku);
                                $set('name', $product->name);
                                $set('quantity', '');
                                $set('unit_cost', $product->purchase_price);
                                $set('formatted_unit_cost', number_format($product->purchase_price, 2));
                            })
                            ->rules([
                                function ($component) {
                                    return function (string $attribute, $value, \Closure $fail) use ($component) {
                                        $items = $component->getContainer()->getParentComponent()->getState();
                                        $selected = array_column($items, $component->getName());

                                        if (count(array_unique($selected)) < count($selected)) {
                                            $fail('You can only select one option.');
                                        }
                                    };
                                },
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('quantity')
                            ->lazy()
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
                                $cost = $get('unit_cost');
                                if (empty($cost)) {
                                    $cost = 0;
                                }
                                $set('total_cost', number_format($cost * $state, 2));
                            })
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('formatted_unit_cost')
                            ->suffix($currency)
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
                                $quantity = $get('quantity');
                                if (empty($quantity)) {
                                    $quantity = 0;
                                }
                                $set('total_cost', number_format($quantity * $state, 2));
                            })
                            ->disabled(),
                        Forms\Components\TextInput::make('total_cost')
                            ->suffix($currency)
                            ->disabled(),
                    ])
                    ->mutateRelationshipDataBeforeFillUsing(function (array $data) {
                        $data['total_cost'] = number_format($data['quantity'] * $data['unit_cost'], 2);

                        return $data;
                    })
                    ->live()
                    ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                        self::updateTotals($get, $set);
                    })
                    ->columnSpan('full'),
                Forms\Components\Section::make('Payment')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('sub_total')
                            ->suffix($currency)
                            ->lazy()
                            ->disabled(),
                        TextInput::make('shipping_fee')
                            ->numeric()
                            ->lazy()
                            ->default(0)
                            ->minValue(0)
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                self::updateTotals($get, $set);
                            }),
                        Forms\Components\TextInput::make('total_amount')
                            ->minValue(0)
                            ->suffix($currency)
                            ->lazy()
                            ->disabled()
                            ->dehydrated()
                            ->mutateDehydratedStateUsing(function ($state) {
                                return floatval(str_replace(',', '', $state));
                            }),
                        Forms\Components\Group::make([
                            Forms\Components\Select::make('payment_type_id')
                                ->relationship('paymentType', 'name')
                                ->required(),
                            Forms\Components\TextInput::make('reference_number'),
                        ])
                            ->columns(2),
                        Forms\Components\Group::make([
                            Forms\Components\TextInput::make('paid_amount')
                                ->suffix($currency)
                                ->required()
                                ->default(0)
                                ->minValue(0)
                                ->maxValue(fn ($get) => floatval(str_replace(',', '', $get('total_amount'))) ?? 0)
                                ->numeric(),
                            Forms\Components\Actions::make([
                                Forms\Components\Actions\Action::make('pay_full')
                                    ->label('Pay in full')
                                    ->color('success')
                                    ->action(
                                        fn ($set, $get) => $set(
                                            'paid_amount',
                                            str_replace(',', '', $get('total_amount'))
                                        )
                                    ),
                            ]),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('purchase_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('order_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('supplier.company_name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('shipping_fee')
                    ->sortable()
                    ->money(fn ($record) => $record->company->getCurrency())
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money(filament()->getTenant()->getCurrency())
                            ->label('Total Shipping Fee'),
                    ]),
                Tables\Columns\TextColumn::make('remaining_amount')
                    ->sortable()
                    ->money(fn ($record) => $record->company->getCurrency())
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money(filament()->getTenant()->getCurrency())
                            ->label('Total Remaining Amount'),
                    ]),
                Tables\Columns\TextColumn::make('total_amount')
                    ->sortable()
                    ->money(fn ($record) => $record->company->getCurrency())
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money(filament()->getTenant()->getCurrency())
                            ->label('Total Amount'),
                    ]),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Created By'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                DateRangeFilter::make('order_date'),
            ])
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->exporter(PurchaseOrderExporter::class),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('Pay Amount')
                        ->form([
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
                                    Forms\Components\Actions\Action::make('pay_in_full')
                                        ->icon('heroicon-m-arrow-down-tray')
                                        ->action(function (Forms\Set $set, $state, $record) {
                                            $set('paid_amount', $record->remaining_amount);
                                        })
                                ),
                        ])
                        ->color('info')
                        ->icon('heroicon-m-banknotes')
                        ->visible(fn ($record) => $record->remaining_amount > 0)
                        ->action(function ($record, array $data) {
                            $record->paid_amount += $data['paid_amount'];
                            $record->save();
                        }),
                    Tables\Actions\Action::make('Complete')
                        ->requiresConfirmation()
                        ->color('success')
                        ->icon('heroicon-m-check')
                        ->action(fn ($record) => $record->setCompleted())
                        ->visible(fn ($record) => $record->isAvailable()),
                    Tables\Actions\Action::make('Cancel')
                        ->requiresConfirmation()
                        ->color('danger')
                        ->icon('heroicon-m-x-mark')
                        ->action(fn ($record) => $record->setCancelled())
                        ->visible(fn ($record) => $record->isAvailable()),
                ]),
            ])
            ->defaultSort('order_date', 'desc');
    }

    public static function getWidgets(): array
    {
        return [
            PurchaseOrderLimit::class,
        ];
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchaseOrders::route('/'),
            'create' => Pages\CreatePurchaseOrder::route('/create'),
            'view' => Pages\ViewPurchaseOrder::route('/{record}'),
        ];
    }

    public static function updateTotals(Forms\Get $get, Forms\Set $set): void
    {
        // Retrieve all selected products and remove empty rows
        $selectedProducts = collect($get('purchaseOrderItems'))->filter(
            fn ($item) => ! empty($item['product_id']) && ! empty($item['quantity'])
        );

        // Calculate subtotal based on the selected products and quantities
        $subtotal = $selectedProducts->reduce(function ($subtotal, $product) {
            return $subtotal + ((float) $product['unit_cost'] * $product['quantity']);
        }, 0);
        $set('sub_total', number_format($subtotal, 2));

        $shippingFee = (float) $get('shipping_fee');

        // Update the state with the new values
        $set('total_amount', number_format($subtotal + $shippingFee, 2));
    }
}
