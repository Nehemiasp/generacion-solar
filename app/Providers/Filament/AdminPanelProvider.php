<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\AlertasActivas;
use App\Filament\Widgets\EstadisticasNacionales;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('Generación solar · Administración')
            ->colors([
                'primary' => Color::hex('#0F5C63'),
                'danger' => Color::hex('#B32747'),
                'warning' => Color::hex('#A9831A'),
            ])
            ->font('Archivo')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->widgets([
                EstadisticasNacionales::class,
                AlertasActivas::class,
            ])
            ->navigationItems([
                NavigationItem::make('Tablero público')->url('/', shouldOpenInNewTab: true)->icon('heroicon-o-chart-bar')->sort(10),
                NavigationItem::make('Mapa público')->url('/mapa', shouldOpenInNewTab: true)->icon('heroicon-o-map')->sort(11),
                NavigationItem::make('Documentación API')->url('/docs/api', shouldOpenInNewTab: true)->icon('heroicon-o-code-bracket')->sort(12),
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
