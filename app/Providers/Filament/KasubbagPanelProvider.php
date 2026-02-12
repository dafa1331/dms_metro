<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\Facades\Auth;

class KasubbagPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('kasubbag')
            ->path('kasubbag')
            ->login()
            ->brandName('DMS BKPSDM')
            ->favicon(asset('logo.png'))
            ->authGuard('web')

            // ✅ MIDDLEWARE WEB LENGKAP (WAJIB)
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
            
            ->colors([
                'primary' => Color::Blue,
            ])

            // ✅ ROLE CHECK SETELAH LOGIN
            ->authMiddleware([
                Authenticate::class,
                //  'panel.kasubbag',
            ])

            ->discoverResources(
                in: app_path('Filament/Kasubbag/Resources'),
                for: 'App\\Filament\\Kasubbag\\Resources'
            )

            ->discoverPages(
                in: app_path('Filament/Kasubbag/Pages'),
                for: 'App\\Filament\\Kasubbag\\Pages'
            );
    }
}
