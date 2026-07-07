<?php

namespace App\Http\Middleware;

use App\Models\Subscription;
use Closure;
use Illuminate\Http\Request;

class CheckSubscription
{
    /**
     * Routes accessibles sans abonnement actif
     */
    private array $freeRoutes = [
        'admin', 'admin/*',
        'filament/*',
        'export/*',
    ];

    public function handle(Request $request, Closure $next, string $feature = '')
    {
        $user = auth()->user();
        if (!$user) return $next($request);

        $business = $user->business;
        if (!$business) return $next($request);

        // Plan free → toujours OK
        if ($business->plan === 'free') return $next($request);

        // Vérifie l'abonnement actif
        $subscription = Subscription::where('business_id', $business->id)
            ->where('status', 'active')
            ->where(fn($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>', now()))
            ->latest()
            ->first();

        if (!$subscription) {
            return redirect()->route('payment.pricing')
                ->with('warning', 'Votre abonnement a expiré. Renouvelez pour continuer à utiliser WhatsAppBizAI.');
        }

        return $next($request);
    }
}
