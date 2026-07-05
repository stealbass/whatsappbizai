<?php

namespace App\Http\Composers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ClientComposer
{
    public function compose(View $view): void
    {
        $user = Auth::user();

        if ($user && $user->business) {
            $business = $user->business;
            $sidebarStats = [
                'contacts'      => $business->contacts()->count(),
                'invoices'      => $business->invoices()->count(),
                'quotes'        => $business->quotes()->count(),
                'conversations' => $business->conversations()->count(),
            ];
        } else {
            $business = null;
            $sidebarStats = [
                'contacts'      => 0,
                'invoices'      => 0,
                'quotes'        => 0,
                'conversations' => 0,
            ];
        }

        $view->with(compact('business', 'sidebarStats'));
    }
}
