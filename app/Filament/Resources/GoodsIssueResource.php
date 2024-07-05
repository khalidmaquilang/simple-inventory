<?php

namespace App\Filament\Resources;

use App\Enums\GoodsIssueTypeEnum;
use App\Filament\Resources\GoodsIssueResource\Pages;
use App\Filament\Resources\GoodsIssueResource\Widgets\GoodsIssueLimit;
use App\Models\Customer;
use App\Models\GoodsIssue;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GoodsIssueResource extends Resource
{
    protected static ?string $model = GoodsIssue::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box-arrow-down';

    protected static ?string $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('issue_date')
                    ->required(),
                Forms\Components\Select::make('type')
                    ->options(GoodsIssueTypeEnum::class)
                    ->required(),
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required(),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->minValue(0),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('issue_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sale.invoice_number')
                    ->label('Invoice Number')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('gin_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Created By')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge(),
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
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getWidgets(): array
    {
        return [
            GoodsIssueLimit::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGoodsIssues::route('/'),
            'create' => Pages\CreateGoodsIssue::route('/create'),
        ];
    }
}
