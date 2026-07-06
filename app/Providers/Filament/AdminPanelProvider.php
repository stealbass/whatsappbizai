<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\LanguageSwitcher;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        // Set locale BEFORE panel boots so static __() calls work
        $locale = Session::get('locale') ?? Cookie::get('wbai_lang') ?? config('app.locale', 'fr');
        if (in_array($locale, ['fr', 'en'])) {
            App::setLocale($locale);
            config(['app.locale' => $locale]);
        }

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors(['primary' => Color::Sky])
            ->brandName('WhatsAppBizAI')
            ->favicon(asset('favicon.ico'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([Pages\Dashboard::class])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([LanguageSwitcher::class])
            ->renderHook(
                'panels::topbar.end',
                fn() => view('filament.widgets.language-switcher')
            )

            ->navigationGroups([
                NavigationGroup::make()->label('Administration'),
                NavigationGroup::make()->label('Messagerie'),
                NavigationGroup::make()->label('Documents'),
                NavigationGroup::make()->label('Gestion Financière'),
                NavigationGroup::make()->label('Catalogue'),
                NavigationGroup::make()->label('Marketing'),
                NavigationGroup::make()->label('Paramètres'),
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
                \App\Http\Middleware\SetLocale::class,
            ])
            ->authMiddleware([Authenticate::class]);
    }
}
