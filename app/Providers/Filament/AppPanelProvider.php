<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Tenancy\EditCompanyProfile;
use App\Filament\Pages\Tenancy\RegisterCompany;
use App\Filament\Resources\CustomerResource;
use App\Filament\Resources\ProductResource;
use App\Filament\Resources\PurchaseOrderResource\Widgets\PurchaseOrdersChart;
use App\Filament\Resources\SaleResource\Widgets\SalesChart;
use App\Filament\Resources\SupplierResource;
use App\Filament\Widgets\OverlookWidget;
use App\Http\Middleware\CompaniesPermission;
use App\Models\Company;
use Awcodes\Overlook\OverlookPlugin;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('app')
            ->path('/')
            ->registration()
            ->passwordReset()
            ->login()
            ->emailVerification()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->favicon('favicon.ico')
            ->font('Poppins')
            ->userMenuItems([
                MenuItem::make()
                    ->label('Admin')
                    ->url('/admin')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->visible(function () {
                        $company = filament()->getTenant();
                        if (empty($company)) {
                            return false;
                        }

                        return $company->isSuperCompany();
                    }),
            ])
            ->navigationGroups([
                'Procurement' => NavigationGroup::make(fn () => 'Procurement'),
                'Inventory' => NavigationGroup::make(fn () => 'Inventory'),
                'Sale' => NavigationGroup::make(fn () => 'Sale'),
                'Finance' => NavigationGroup::make(fn () => 'Finance'),
                'Settings' => NavigationGroup::make(fn () => 'Settings'),
            ])
            ->maxContentWidth(MaxWidth::Full)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                OverlookWidget::class,
                PurchaseOrdersChart::class,
                SalesChart::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->databaseNotifications()
            ->authMiddleware([
                Authenticate::class,
            ])
            ->viteTheme('resources/css/filament/app/theme.css')
            ->plugins([
                FilamentShieldPlugin::make(),
                OverlookPlugin::make()
                    ->sort(2)
                    ->columns([
                        'default' => 1,
                        'sm' => 2,
                        'md' => 3,
                        'lg' => 3,
                        'xl' => 3,
                        '2xl' => null,
                    ])
                    ->includes([
                        CustomerResource::class,
                        SupplierResource::class,
                        ProductResource::class,
                    ]),
                FilamentApexChartsPlugin::make(),
                FilamentEditProfilePlugin::make()
                    ->setTitle('My Profile')
                    ->setNavigationLabel('My Profile')
                    ->setIcon('heroicon-o-user')
                    ->setNavigationGroup('Settings')
                    ->shouldShowDeleteAccountForm(false),
            ])
            ->tenant(Company::class, slugAttribute: 'slug')
            ->tenantRegistration(RegisterCompany::class)
            ->tenantProfile(EditCompanyProfile::class)
            ->tenantMiddleware([
                CompaniesPermission::class,
            ], isPersistent: true)
            ->renderHook(
                'panels::head.start',
                fn () => view('analyticsTag'),
            );
    }
}
