<?php

namespace App\Filament\Pages;

use App\Http\Middleware\OnboardingMiddleware;
use App\Models\Currency;
use App\Models\Setting;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;

class Settings extends Page implements HasForms
{
    use HasPageShield, InteractsWithForms;

    public ?array $data = [];

    protected static string $view = 'filament.pages.settings';

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Admin Menu';

    protected static ?int $navigationSort = 4;

    protected static string|array $withoutRouteMiddleware = [OnboardingMiddleware::class];

    public function mount(): void
    {
        $setting = Setting::first();
        if (empty($setting)) {
            return;
        }

        $this->form->fill($setting->attributesToArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('company_name')
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->required(),
                TextInput::make('email')
                    ->email()
                    ->required(),
                Textarea::make('address')
                    ->required(),
                FileUpload::make('company_logo')
                    ->image()
                    ->maxSize(2048),
                Select::make('currency')
                    ->options(Currency::getCurrencyList())
                    ->required(),

            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            $setting = Setting::first();
            if (empty($setting)) {
                Setting::create(array_merge($data, ['company_id' => session('company_id')]));
            } else {
                $setting->update($data);
            }
        } catch (Halt $exception) {
            return;
        }

        Notification::make()
            ->success()
            ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))
            ->send();
    }
}
