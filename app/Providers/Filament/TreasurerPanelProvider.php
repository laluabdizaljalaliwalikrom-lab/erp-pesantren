<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\Facades\Storage;

class TreasurerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('treasurer')
            ->path('treasurer')
            ->login()
            ->brandName(fn () => settings()->pesantren_name ?? 'Bendahara ERP')
            ->brandLogo(fn () => settings()->logo_path ? Storage::url(settings()->logo_path) : null)
            ->brandLogoHeight('2.5rem')
            ->favicon(fn () => settings()->favicon_path ? Storage::url(settings()->favicon_path) : null)
            ->colors([
                'primary' => settings()->primary_color ?? Color::Emerald,
            ])
            ->spa()
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Treasurer/Resources'), for: 'App\Filament\Treasurer\Resources')
            ->discoverPages(in: app_path('Filament/Treasurer/Pages'), for: 'App\Filament\Treasurer\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Treasurer/Widgets'), for: 'App\Filament\Treasurer\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
