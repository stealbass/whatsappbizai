<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleCorruptedCookies
{
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        try {
            $cookies = $request->cookies->all();
            $encrypter = app('encrypter');

            if (!$encrypter) {
                return;
            }

            foreach ($cookies as $name => $value) {
                if (!is_string($value)) {
                    continue;
                }
                try {
                    $encrypter->decrypt($value, false);
                } catch (\Throwable $e) {
                    $response->headers->removeCookie($name);
                }
            }
        } catch (\Throwable $e) {
            // Key unavailable — ignore
        }
    }
}
