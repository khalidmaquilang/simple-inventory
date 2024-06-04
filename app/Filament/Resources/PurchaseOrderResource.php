<?php

namespace App\Filament\Resources;

use App\Enums\PurchaseOrderEnum;
use App\Filament\Resources\PurchaseOrderResource\Pages;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Setting;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        $currency = Setting::getCurrency();

        return $form
            ->schema([
                Forms\Components\DatePicker::make('order_date')
                    ->required(),
                Forms\Components\DatePicker::make('expected_delivery_date'),
                Forms\Components\TextInput::make('purchase_code')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('supplier_id')
                    ->relationship('supplier', 'id')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options(PurchaseOrderEnum::class)
                    ->required(),
                TableRepeater::make('purchaseOrderItems')
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
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if (empty($state)) {
                                    $set('sku', '');
                                    $set('name', '');

                                    return;
                                }

                                $product = Product::find($state);
                                $set('sku', $product->sku);
                                $set('name', $product->name);
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
                            ->suffix($currency)
                            ->lazy()
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
                                $quantity = $get('quantity');
                                if (empty($quantity)) {
                                    $quantity = 0;
                                }
                                $set('total_cost', number_format($quantity * $state, 2));
                            })
                            ->required()
                            ->numeric(),
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
                        Forms\Components\TextInput::make('paid_amount')
                            ->required()
                            ->suffix($currency)
                            ->minValue(0)
                            ->maxValue(fn ($get) => $get('total_amount') ?? 0)
                            ->numeric(),
                        Forms\Components\TextInput::make('total_amount')
                            ->minValue(0)
                            ->suffix($currency)
                            ->lazy()
                            ->disabled()
                            ->dehydrated()
                            ->numeric(),
                        Forms\Components\Select::make('payment_type_id')
                            ->relationship('paymentType', 'name')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $currency = Setting::getCurrency();

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('purchase_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('supplier.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->formatStateUsing(fn ($state): string => number_format($state, 2).' '.$currency),
                Tables\Columns\TextColumn::make('remaining_amount')
                    ->getStateUsing(function ($record): float {
                        return $record->total_amount - $record->paid_amount;
                    })
                    ->formatStateUsing(fn ($state): string => number_format($state, 2).' '.$currency),
                Tables\Columns\TextColumn::make('order_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListPurchaseOrders::route('/'),
            'create' => Pages\CreatePurchaseOrder::route('/create'),
            'edit' => Pages\EditPurchaseOrder::route('/{record}/edit'),
        ];
    }

    public static function updateTotals(Forms\Get $get, Forms\Set $set): void
    {
        // Retrieve all selected products and remove empty rows
        $selectedProducts = collect($get('purchaseOrderItems'))->filter(fn ($item) => ! empty($item['product_id']) && ! empty($item['quantity']));

        // Calculate subtotal based on the selected products and quantities
        $subtotal = $selectedProducts->reduce(function ($subtotal, $product) {
            return $subtotal + ($product['unit_cost'] * $product['quantity']);
        }, 0);

        // Update the state with the new values
        $set('total_amount', number_format($subtotal, 2));
    }
}
