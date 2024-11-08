<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InviteResource\Pages;
use App\Filament\Resources\InviteResource\Widgets\UserLimit;
use App\Mail\UserInvitationMail;
use App\Models\Invite;
use App\Models\Role;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Unique;

class InviteResource extends Resource
{
    protected static ?string $model = Invite::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule) {
                        return $rule->where('company_id', Filament::getTenant()->id);
                    })
                    ->required()
                    ->autofocus()
                    ->autocomplete(false),
                Forms\Components\Select::make('roles')
                    ->options(function () {
                        return Role::where('company_id', Filament::getTenant()->id)->pluck('name', 'name')->toArray();
                    })
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('resend')
                    ->label('Resend')
                    ->icon('heroicon-o-inbox-stack')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        Mail::to($record->email)
                            ->queue(
                                (new UserInvitationMail($record))
                                    ->onQueue('short-running-queue')
                            );

                        Notification::make()
                            ->success()
                            ->title('Invitation sent')
                            ->body('Invitation has been successfully sent to the recipient.')
                            ->send();
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getWidgets(): array
    {
        return [
            UserLimit::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageInvites::route('/'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Invite User';
    }
}
