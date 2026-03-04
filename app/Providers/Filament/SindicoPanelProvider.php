<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use App\Filament\Sindico\Pages\EditProfile;
use Illuminate\Contracts\View\View;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class SindicoPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->globalSearch(false)
            ->sidebarCollapsibleOnDesktop()
            ->id('sindico')
            ->path('sindico')
            ->authGuard('client')
            ->login()
            ->profile(EditProfile::class)
            ->brandName('RepassesJá')
            ->brandLogo(fn (): View => view('filament.brand-logo'))
            ->colors([
                'primary' => Color::hex('#FC6E20'),
                'warning' => Color::hex('#FFE7D0'),
                'danger' => Color::Red,
                'success' => Color::Green,
            ])
            ->discoverResources(in: app_path('Filament/Sindico/Resources'), for: 'App\Filament\Sindico\Resources')
            ->discoverPages(in: app_path('Filament/Sindico/Pages'), for: 'App\Filament\Sindico\Pages')
            ->pages([])
            ->discoverWidgets(in: app_path('Filament/Sindico/Widgets'), for: 'App\Filament\Sindico\Widgets')
            ->widgets([])
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
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
