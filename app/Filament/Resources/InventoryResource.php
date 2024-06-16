<?php

namespace App\Filament\Resources;

use App\Filament\Exports\InventoryExporter;
use App\Filament\Resources\InventoryResource\Pages;
use App\Models\Inventory;
use App\Models\Product;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;

class InventoryResource extends Resource
{
    protected static ?string $model = Inventory::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?int $navigationSort = 9;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->createOptionForm(Product::getForm())
                    ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule) {
                        return $rule->where('company_id', Filament::getTenant()->id);
                    })
                    ->required(),
                Forms\Components\TextInput::make('quantity_on_hand')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('average_cost')
                    ->suffix(Filament::getTenant()->getCurrency())
                    ->default(0)
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.sku')
                    ->label('SKU')
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity_on_hand')
                    ->sortable(),
                Tables\Columns\TextColumn::make('average_cost')
                    ->money(fn ($record) => $record->company->getCurrency()),
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
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->exporter(InventoryExporter::class),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('adjust_avg_cost')
                    ->label('Adjust Avg Cost')
                    ->authorize('update', Inventory::class)
                    ->fillForm(fn ($record): array => [
                        'average_cost' => $record->average_cost,
                    ])
                    ->form([
                        TextInput::make('average_cost')
                            ->minValue(0)
                            ->numeric()
                            ->required(),
                    ])
                    ->color('info')
                    ->icon('heroicon-m-chart-pie')
                    ->action(function ($record, array $data) {
                        $record->average_cost = $data['average_cost'];
                        $record->save();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
            'index' => Pages\ListInventories::route('/'),
            'create' => Pages\CreateInventory::route('/create'),
            'view' => Pages\ViewInventory::route('/{record}'),
        ];
    }
}
