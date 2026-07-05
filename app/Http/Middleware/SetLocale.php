<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->cookie('wbai_lang');

        if ($locale && in_array($locale, ['fr', 'en'])) {
            App::setLocale($locale);
            config(['app.locale' => $locale]);
        }

        return $next($request);
    }
}
