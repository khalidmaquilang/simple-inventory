<?php

namespace App\Filament\Resources;

use App\Enums\DiscountTypeEnum;
use App\Filament\Exports\SaleExporter;
use App\Filament\Resources\SaleResource\Pages;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Setting;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
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

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        $currency = Setting::getCurrency();

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
                    ->headers([
                        Header::make('sku')
                            ->label('SKU'),
                        Header::make('Product Name'),
                        Header::make('Quantity'),
                        Header::make('Unit Cost'),
                        Header::make('Total Cost'),
                    ])
                    ->schema([
                        Forms\Components\TextInput::make('sku')
                            ->readOnly(),
                        Forms\Components\Hidden::make('name'),
                        Forms\Components\Select::make('product_id')
                            ->relationship('product', 'name')
                            ->lazy()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if (empty($state)) {
                                    $set('sku', '');
                                    $set('name', '');
                                    $set('unit_cost', '');

                                    return;
                                }

                                $product = Product::find($state);
                                $set('sku', $product->sku);
                                $set('name', $product->name);
                                $set('unit_cost', $product->selling_price);
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
                        Forms\Components\TextInput::make('unit_cost')
                            ->lazy()
                            ->suffix($currency)
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
                                $quantity = $get('quantity');
                                if (empty($quantity)) {
                                    $quantity = 0;
                                }
                                $set('total_cost', number_format($quantity * $state, 2));
                            })
                            ->disabled()
                            ->dehydrated(),
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
                        Forms\Components\TextInput::make('vat')
                            ->label('VAT (Value-Added Tax)')
                            ->suffix('%')
                            ->lazy()
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
                        Forms\Components\TextInput::make('total_amount')
                            ->suffix($currency)
                            ->minValue(0)
                            ->disabled()
                            ->dehydrated()
                            ->numeric(),
                        Forms\Components\Select::make('payment_type_id')
                            ->relationship('paymentType', 'name')
                            ->required(),
                        Forms\Components\Group::make([
                            Forms\Components\TextInput::make('paid_amount')
                                ->suffix($currency)
                                ->required()
                                ->minValue(0)
                                ->maxValue(fn ($get) => floatval(str_replace(',', '', $get('total_amount'))) ?? 0)
                                ->numeric(),
                            Forms\Components\Actions::make([
                                Forms\Components\Actions\Action::make('pay_full')
                                    ->label('Pay in full')
                                    ->color('success')
                                    ->action(fn ($set, $get) => $set('paid_amount', $get('total_amount')))
                                    ->visible(fn ($operation) => $operation === 'create'),
                            ]),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $currency = Setting::getCurrency();

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sale_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pay_until')
                    ->label('Due Date')
                    ->formatStateUsing(fn ($state) => now()->addDays($state)->format('M d, Y')),
                Tables\Columns\TextColumn::make('total_amount')
                    ->formatStateUsing(fn ($state): string => number_format($state, 2).' '.$currency),
                Tables\Columns\TextColumn::make('formatted_remaining_amount')
                    ->label('Remaining Amount')
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('paymentType.name')
                    ->numeric()
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
                        ->action(function ($record) use ($table) {
                            $data = $table->getLivewire()->getMountedTableAction()->getFormData();

                            $record->paid_amount += $data['paid_amount'];
                            $record->save();
                        }),
                    Tables\Actions\Action::make('Download Invoice')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->url(fn (Sale $record) => route('sales.generate-invoice', $record))
                        ->openUrlInNewTab(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSale::route('/create'),
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
        $selectedProducts = collect($get('saleItems'))->filter(fn ($item) => ! empty($item['product_id']) && ! empty($item['quantity']));

        // Calculate subtotal based on the selected products and quantities
        $subtotal = $selectedProducts->reduce(function ($subtotal, $product) {
            return $subtotal + ($product['unit_cost'] * $product['quantity']);
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
        $subTotal = $get('sub_total');
        $vatField = $get('vat');
        $discount = $get('discount');
        $discountType = $get('discount_type');

        if (empty($subTotal) || empty($vatField)) {
            $subTotal = 0;
        }

        if (! empty($discount)) {
            $subTotal = self::calculateAfterDiscount($subTotal, $discount, $discountType);
        }

        $vat = $subTotal * ($vatField / 100);

        $set('total_amount', number_format($subTotal + $vat, 2));
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
