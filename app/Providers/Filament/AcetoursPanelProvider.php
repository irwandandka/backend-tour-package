<?php

namespace App\Providers\Filament;

use App\Filament\Acetours\Resources\CityResource;
use App\Filament\Acetours\Resources\CountryResource;
use App\Filament\Acetours\Resources\PermissionResource;
use App\Filament\Acetours\Resources\ProductResource;
use App\Filament\Acetours\Resources\RoleResource;
use App\Filament\Resources\UserResource;
use Filament\Http\Middleware\{Authenticate, AuthenticateSession, DisableBladeIconComponents, DispatchServingFilamentEvent};
use Filament\{Pages, Panel, PanelProvider, Widgets};
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\{AddQueuedCookiesToResponse, EncryptCookies};
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AcetoursPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('acetours')
            ->path('acetours')
            // ->viteTheme('resources/css/filament-custom.css')
            ->login()
            ->resources([
                UserResource::class,
                // RoleResource::class,
                // PermissionResource::class,
                CityResource::class,
                CountryResource::class,
                ProductResource::class,
            ])
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Acetours/Resources'), for: 'App\\Filament\\Acetours\\Resources')
            ->discoverPages(in: app_path('Filament/Acetours/Pages'), for: 'App\\Filament\\Acetours\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Acetours/Widgets'), for: 'App\\Filament\\Acetours\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
