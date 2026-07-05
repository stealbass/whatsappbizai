<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfInstalled
{
    public function handle(Request $request, Closure $next)
    {
        if (config('app.installed', false)) {
            return redirect('/');
        }

        return $next($request);
    }
}
