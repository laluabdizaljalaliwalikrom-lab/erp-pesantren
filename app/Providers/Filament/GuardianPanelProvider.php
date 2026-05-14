<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Guardian\Pages\GuardianDashboard;
use App\Filament\Guardian\Pages\GuardianLogin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\Facades\Storage;

class GuardianPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('guardian')
            ->path('wali')
            ->viteTheme('resources/css/filament/guardian/theme.css')
            ->authGuard('guardian')
            ->login(GuardianLogin::class)
            ->registration(\App\Filament\Guardian\Pages\Auth\GuardianRegister::class)
            ->brandName(fn () => settings()->pesantren_name ?? 'Portal Wali')
            ->brandLogo(fn () => settings()->logo_path ? Storage::url(settings()->logo_path) : null)
            ->brandLogoHeight('2.5rem')
            ->favicon(fn () => settings()->favicon_path ? Storage::url(settings()->favicon_path) : null)
            ->colors([
                'primary' => settings()->primary_color ?? Color::Emerald,
            ])
            ->discoverResources(
                in: app_path('Filament/Guardian/Resources'),
                for: 'App\\Filament\\Guardian\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/Guardian/Pages'),
                for: 'App\\Filament\\Guardian\\Pages'
            )
            ->pages([
                GuardianDashboard::class,
                \App\Filament\Guardian\Pages\AccountPendingApproval::class,
            ])
            ->discoverWidgets(
                in: app_path('Filament/Guardian/Widgets'),
                for: 'App\\Filament\\Guardian\\Widgets'
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                \Filament\Http\Middleware\AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                \Filament\Http\Middleware\Authenticate::class,
                \App\Http\Middleware\CheckGuardianApproval::class,
            ])
            ->renderHook(
                \Filament\View\PanelsRenderHook::HEAD_START,
                fn () => new \Illuminate\Support\HtmlString('<style>.fi-simple-layout img, .fi-simple-layout svg { max-height: 4.5rem !important; width: auto !important; }</style>'),
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
                fn () => view('filament.guardian.components.login-footer'),
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::AUTH_REGISTER_FORM_AFTER,
                fn () => view('filament.guardian.components.login-footer'),
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::BODY_END,
                fn () => new \Illuminate\Support\HtmlString(
                    view('filament.guardian.hooks.midtrans-js')->render() . 
                    view('filament.guardian.components.admin-contact-floating')->render()
                ),
            );
    }
}
