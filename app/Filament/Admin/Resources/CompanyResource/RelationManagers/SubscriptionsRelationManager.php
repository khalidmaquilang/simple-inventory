<?php

namespace App\Filament\Admin\Resources\CompanyResource\RelationManagers;

use App\Enums\BillingCycleEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\SubscriptionStatusEnum;
use App\Models\Plan;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SubscriptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'subscriptions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('total_amount')
                    ->default(1),
                Forms\Components\Select::make('plan_id')
                    ->relationship('plan', 'name')
                    ->required(),
                Forms\Components\DatePicker::make('start_date')
                    ->required(),
                Forms\Components\DatePicker::make('end_date'),
                Forms\Components\TextInput::make('extra_users')
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('plan.name')
            ->columns([
                Tables\Columns\TextColumn::make('plan.name'),
                Tables\Columns\TextColumn::make('start_date')
                    ->date(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('extra_users')
                    ->badge(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('PHP'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data) {
                        return $this->beforeCreate($data);
                    })
                    ->after(fn ($record) => $this->afterCreate($record))
                    ->createAnother(false),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->icon('heroicon-m-eye')
                    ->color('gray')
                    ->label('View')
                    ->action(fn ($record) => redirect(route('filament.admin.resources.subscriptions.view', [$record]))),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('renew')
                    ->icon('heroicon-m-banknotes')
                    ->color('success')
                    ->fillForm(fn ($record): array => [
                        'amount' => $record->total_amount,
                    ])
                    ->form($this->renewForm())
                    ->action(function (array $data, $record): void {
                        $record->updateEndDate();
                        $record->payments()->create($data);
                    })
                    ->successNotificationTitle('Payment Updated')
                    ->visible(fn ($record) => $record->isActive()),
            ])
            ->bulkActions([

            ])
            ->defaultSort('created_at', 'desc');
    }

    /**
     * @return array
     */
    protected function renewForm(): array
    {
        return [
            Forms\Components\DatePicker::make('payment_date')
                ->required(),
            TextInput::make('amount')
                ->numeric()
                ->required(),
            TextInput::make('payment_method')
                ->required(),
            Select::make('status')
                ->options(PaymentStatusEnum::class)
                ->required(),
        ];
    }

    /**
     * @param  array  $data
     * @return array
     */
    protected function beforeCreate(array $data): array
    {
        $plan = Plan::find($data['plan_id']);
        if (empty($plan)) {
            return [];
        }

        $data['status'] = SubscriptionStatusEnum::ACTIVE;
        $data['end_date'] = $plan->billing_cycle === BillingCycleEnum::MONTHLY ? (new Carbon(
            $data['start_date']
        ))->addMonth() : (new Carbon($data['start_date']))->addYear();
        $data['total_amount'] = $plan->price + ($data['extra_users'] * 100); //100php per user

        $subscription = $this->ownerRecord->getActiveSubscription();
        if (! empty($subscription)) {
            $subscription->cancel(); // cancel latest subscription
        }

        return $data;
    }

    /**
     * @param  $record
     * @return void
     */
    protected function afterCreate($record): void
    {
        $record->payments()->create([
            'payment_date' => now(),
            'amount' => $record->total_amount,
            'payment_method' => 'system',
            'status' => PaymentStatusEnum::SUCCESS,
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
