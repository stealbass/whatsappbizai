<?php

namespace App\Http\Composers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ClientComposer
{
    public function compose(View $view): void
    {
        $user = Auth::user();

        if (! $user || ! $user->business) {
            return;
        }

        $business = $user->business;
        $sidebarStats = [
            'contacts'      => $business->contacts()->count(),
            'invoices'      => $business->invoices()->count(),
            'quotes'        => $business->quotes()->count(),
            'conversations' => $business->conversations()->count(),
        ];

        $view->with(compact('business', 'sidebarStats'));
    }
}
