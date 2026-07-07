<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // Priority: session > cookie > config default
        $locale = Session::get('locale')
            ?? $request->cookie('wbai_lang')
            ?? config('app.locale', 'fr');

        if (!in_array($locale, ['fr', 'en'])) {
            $locale = config('app.locale', 'fr');
        }

        App::setLocale($locale);
        config(['app.locale' => $locale]);

        return $next($request);
    }
}
