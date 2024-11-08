<?php

namespace App\Filament\Resources;

use App\Enums\DiscountTypeEnum;
use App\Filament\Exports\SaleExporter;
use App\Filament\Resources\SaleResource\Pages;
use App\Filament\Resources\SaleResource\Widgets\SaleLimit;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Sale;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Sale';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        $currency = Filament::getTenant()->getCurrency();

        return $form
            ->schema([
                Forms\Components\DatePicker::make('sale_date')
                    ->required(),
                Forms\Components\TextInput::make('pay_until')
                    ->label('Due in (days)')
                    ->hint('0 if today')
                    ->numeric()
                    ->required(),
                Forms\Components\Select::make('customer_id')
                    ->relationship('customer', 'name')
                    ->createOptionForm(Customer::getForm())
                    ->searchable()
                    ->optionsLimit(10)
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
                TableRepeater::make('saleItems')
                    ->relationship()
                    ->addActionLabel('Click to add more products')
                    ->headers([
                        Header::make('Product Name'),
                        Header::make('Current Stock'),
                        Header::make('Quantity'),
                        Header::make('Unit Cost'),
                        Header::make('Total Cost'),
                    ])
                    ->schema([
                        Forms\Components\Hidden::make('sku'),
                        Forms\Components\Hidden::make('name'),
                        Forms\Components\Hidden::make('unit_cost'),
                        Forms\Components\Select::make('product_id')
                            ->relationship('product', 'sku_name_format')
                            ->lazy()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if (empty($state)) {
                                    $set('sku', '');
                                    $set('name', '');
                                    $set('unit_cost', '');
                                    $set('formatted_unit_cost', '');
                                    $set('quantity', '');
                                    $set('current_stock', '');

                                    return;
                                }

                                $product = Product::find($state);
                                $set('sku', $product->sku);
                                $set('name', $product->name);
                                $set('quantity', '');
                                $set('unit_cost', $product->selling_price);
                                $set('formatted_unit_cost', number_format($product->selling_price, 2));
                                $set('current_stock', $product->current_stock);
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
                        Forms\Components\TextInput::make('current_stock')
                            ->disabled(),
                        Forms\Components\TextInput::make('quantity')
                            ->lazy()
                            ->minValue(1)
                            ->maxValue(function (Forms\Get $get): float {
                                $inventory = Inventory::where('product_id', $get('product_id'))->first();
                                if (empty($inventory)) {
                                    return 0;
                                }

                                return $inventory->quantity_on_hand;
                            })
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
                            ->lazy()
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
                    ->lazy()
                    ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                        self::updateSubTotal($get, $set);
                    })
                    ->columnSpan('full'),
                Forms\Components\Section::make('Payment')
                    ->columns(1)
                    ->schema([
                        Forms\Components\TextInput::make('sub_total')
                            ->lazy()
                            ->suffix($currency)
                            ->disabled(),
                        Forms\Components\TextInput::make('shipping_fee')
                            ->lazy()
                            ->default(0)
                            ->minValue(0)
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                self::updateTotalAmount($get, $set);
                            })
                            ->numeric(),
                        Forms\Components\TextInput::make('vat')
                            ->label('VAT (Value-Added Tax)')
                            ->suffix('%')
                            ->lazy()
                            ->default(0)
                            ->minValue(0)
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                self::updateTotalAmount($get, $set);
                            })
                            ->required()
                            ->numeric(),
                        Forms\Components\Group::make([
                            Forms\Components\TextInput::make('discount')
                                ->default(0)
                                ->lazy()
                                ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                    self::updateTotalAmount($get, $set);
                                })
                                ->numeric(),
                            Forms\Components\ToggleButtons::make('discount_type')
                                ->options(DiscountTypeEnum::class)
                                ->default(DiscountTypeEnum::FIXED->value)
                                ->lazy()
                                ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                    self::updateTotalAmount($get, $set);
                                })
                                ->grouped(),
                        ])
                            ->columns(2),
                        Forms\Components\Hidden::make('total_amount'),
                        Forms\Components\TextInput::make('formatted_total_amount')
                            ->label('Total amount')
                            ->suffix($currency)
                            ->disabled(),
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
                                ->default(0)
                                ->required()
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
                                    )
                                    ->visible(fn ($operation) => $operation === 'create'),
                            ]),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sale_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pay_until')
                    ->label('Due Date')
                    ->formatStateUsing(fn ($state) => now()->addDays($state)->format('M d, Y'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('shipping_fee')
                    ->money(fn ($record) => $record->company->getCurrency())
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money(filament()->getTenant()->getCurrency())
                            ->label('Total Shipping Fee'),
                    ]),
                Tables\Columns\TextColumn::make('remaining_amount')
                    ->money(fn ($record) => $record->company->getCurrency())
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money(filament()->getTenant()->getCurrency())
                            ->label('Total Remaining Amount'),
                    ]),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money(fn ($record) => $record->company->getCurrency())
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money(filament()->getTenant()->getCurrency())
                            ->label('Total Amount'),
                    ]),
                Tables\Columns\TextColumn::make('customer.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('paymentType.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Created By')
                    ->sortable(),
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
                DateRangeFilter::make('sale_date'),
            ])
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->exporter(SaleExporter::class),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('Pay Amount')
                        ->form(Sale::getPayDueAmountForm())
                        ->color('info')
                        ->icon('heroicon-m-banknotes')
                        ->visible(fn ($record) => $record->remaining_amount > 0)
                        ->action(function ($record, array $data) {
                            $record->paid_amount += $data['paid_amount'];
                            $record->reference_number = $data['reference_number'];
                            $record->save();
                        }),
                    Tables\Actions\Action::make('Download Invoice')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->url(fn (Sale $record) => route('app.sales.generate-invoice', [
                            'company' => filament()->getTenant()->id,
                            'sale' => $record,
                        ]))
                        ->openUrlInNewTab(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make(),
                ]),
            ])
            ->defaultSort('sale_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /**
     * @return \class-string[]
     */
    public static function getWidgets(): array
    {
        return [
            SaleLimit::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSale::route('/create'),
            'view' => Pages\ViewSales::route('/{record}'),
        ];
    }

    /**
     * @param  Forms\Get  $get
     * @param  Forms\Set  $set
     * @return void
     */
    public static function updateSubTotal(Forms\Get $get, Forms\Set $set): void
    {
        // Retrieve all selected products and remove empty rows
        $selectedProducts = collect($get('saleItems'))->filter(
            fn ($item) => ! empty($item['product_id']) && ! empty($item['quantity'])
        );

        // Calculate subtotal based on the selected products and quantities
        $subtotal = $selectedProducts->reduce(function ($subtotal, $product) {
            return $subtotal + ((float) $product['unit_cost'] * (float) $product['quantity']);
        }, 0);

        // Update the state with the new values
        $set('sub_total', number_format($subtotal, 2));
        self::updateTotalAmount($get, $set);
    }

    /**
     * @param  Forms\Get  $get
     * @param  Forms\Set  $set
     * @return void
     */
    public static function updateTotalAmount(Forms\Get $get, Forms\Set $set): void
    {
        $subTotal = (float) str_replace(',', '', $get('sub_total'));
        $vatField = (float) $get('vat');
        $shippingFee = (float) $get('shipping_fee');
        $discount = (float) str_replace(',', '', $get('discount'));
        $discountType = $get('discount_type');

        if (empty($subTotal)) {
            $subTotal = 0;
        }

        if (! empty($discount)) {
            $subTotal = self::calculateAfterDiscount($subTotal, $discount, $discountType);
        }

        $subTotal += $shippingFee;

        $vat = 0;
        if (! empty($vatField)) {
            $vat = $subTotal * ($vatField / 100);
        }

        $set('formatted_total_amount', number_format($subTotal + $vat, 2));
        $set('total_amount', $subTotal + $vat);
    }

    /**
     * @param  float  $subTotal
     * @param  float  $discount
     * @param  string  $discountType
     * @return float
     */
    public static function calculateAfterDiscount(float $subTotal, float $discount, string $discountType): float
    {
        if ($discountType === DiscountTypeEnum::FIXED->value) {
            return $subTotal - $discount;
        }

        return $subTotal * (1 - ($discount / 100));
    }
}
