<?php

namespace App\Providers\Filament;

use App\Http\Middleware\SetUserSettings;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Contracts\View\View;
use App\Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class PainelPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->globalSearch(false)
            ->sidebarCollapsibleOnDesktop()
            ->default()
            ->id('painel')
            ->path('painel')
            ->authGuard('web')
            ->login()
            ->profile()
            ->brandName('RepassesJá')
            ->brandLogo(fn (): View => view('filament.brand-logo'))
            ->colors([
                'primary' => Color::hex('#FC6E20'),
                'warning' => Color::hex('#FFE7D0'),
                'danger' => Color::Red,
                'success' => Color::Green,
            ])
            ->userMenuItems([
                MenuItem::make('configuracoes')
                    ->label('Configurações de Taxas')
                    ->icon('heroicon-o-cog')
                    ->url('configuracoes'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class
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
                SetUserSettings::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
