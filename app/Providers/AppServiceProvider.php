<?php

namespace App\Providers;

use App\Models\InvoiceItem;
use App\Models\QuoteItem;
use App\Observers\InvoiceItemObserver;
use App\Observers\QuoteItemObserver;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        InvoiceItem::observe(InvoiceItemObserver::class);
        QuoteItem::observe(QuoteItemObserver::class);

        View::composer('client.*', \App\Http\Composers\ClientComposer::class);

        if (config('app.url')) {
            URL::forceRootUrl(config('app.url'));
        }

        $this->configureLivewireUpdateRoute();
    }

    protected function configureLivewireUpdateRoute(): void
    {
        $basePath = parse_url(config('app.url', ''), PHP_URL_PATH) ?? '';

        if ($basePath !== '') {
            Livewire::setUpdateRoute(function ($handler) use ($basePath) {
                return Route::post($basePath . '/livewire/update', $handler)
                    ->name('livewire.update');
            });
        }
    }
}
