<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $business = $user->business;

        $stats = [
            'contacts'    => $business ? $business->contacts()->count() : 0,
            'invoices'    => $business ? $business->invoices()->count() : 0,
            'quotes'      => $business ? $business->quotes()->count() : 0,
            'conversations' => $business ? $business->conversations()->count() : 0,
        ];

        $recentInvoices = $business
            ? $business->invoices()->latest()->take(5)->get()
            : collect();

        $recentQuotes = $business
            ? $business->quotes()->latest()->take(5)->get()
            : collect();

        return view('dashboard', compact('user', 'business', 'stats', 'recentInvoices', 'recentQuotes'));
    }
}
