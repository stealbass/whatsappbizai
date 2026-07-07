<?php

namespace App\Providers\Filament;

use App\Filament\SuperAdmin\Widgets\LanguageSwitcher;
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

class SuperAdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $locale = Session::get('locale') ?? Cookie::get('wbai_lang') ?? config('app.locale', 'fr');
        if (in_array($locale, ['fr', 'en'])) {
            App::setLocale($locale);
            config(['app.locale' => $locale]);
        }

        return $panel
            ->id('super-admin')
            ->path('super-admin')
            ->login()
            ->colors(['primary' => Color::Indigo])
            ->brandName('WhatsAppBizAI — Super Admin')
            ->favicon(asset('favicon.ico'))
            ->discoverResources(in: app_path('Filament/SuperAdmin/Resources'), for: 'App\\Filament\\SuperAdmin\\Resources')
            ->discoverPages(in: app_path('Filament/SuperAdmin/Pages'), for: 'App\\Filament\\SuperAdmin\\Pages')
            ->pages([Pages\Dashboard::class])
            ->discoverWidgets(in: app_path('Filament/SuperAdmin/Widgets'), for: 'App\\Filament\\SuperAdmin\\Widgets')
            ->widgets([LanguageSwitcher::class])
            ->renderHook('panels::topbar.end', fn() => view('filament.super-admin.widgets.language-switcher'))
            ->navigationGroups([
                NavigationGroup::make()->label('Tableau de bord'),
                NavigationGroup::make()->label('Gestion des Utilisateurs'),
                NavigationGroup::make()->label('Gestion des Entreprises'),
                NavigationGroup::make()->label('Abonnements & Paiements'),
                NavigationGroup::make()->label('Plans & Tarification'),
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
