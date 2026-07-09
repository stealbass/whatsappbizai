<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index()
    {
        $user     = Auth::user();
        $business = $user->business;

        $stats = [
            'contacts'      => $business ? $business->contacts()->count() : 0,
            'invoices'      => $business ? $business->invoices()->count() : 0,
            'quotes'        => $business ? $business->quotes()->count() : 0,
            'conversations' => $business ? $business->conversations()->count() : 0,
        ];

        $recentInvoices = $business
            ? $business->invoices()->latest()->take(5)->get()
            : collect();

        $recentQuotes = $business
            ? $business->quotes()->latest()->take(5)->get()
            : collect();

        // Onboarding checklist — détecté depuis les données existantes, zéro migration
        $hasSentDoc = $business && (
            $business->invoices()->where('whatsapp_sent', true)->exists() ||
            $business->invoices()->where('status', 'sent')->exists() ||
            $business->quotes()->where('whatsapp_sent', true)->exists() ||
            $business->quotes()->where('status', 'sent')->exists()
        );

        $onboarding = [
            'profile'  => $business && $business->address && $business->phone,
            'contact'  => $stats['contacts'] > 0,
            'document' => ($stats['invoices'] + $stats['quotes']) > 0,
            'sent'     => $hasSentDoc,
            'whatsapp' => $business && (!$business->sandbox_mode && $business->whatsapp_phone_number_id),
        ];

        $onboardingDone  = array_sum($onboarding);
        $onboardingTotal = count($onboarding);
        $onboardingComplete  = $onboardingDone === $onboardingTotal;
        $onboardingDismissed = session('onboarding_dismissed', false);
        $showOnboarding = $business && !$onboardingComplete && !$onboardingDismissed;

        return view('client.dashboard', compact(
            'user', 'business', 'stats',
            'recentInvoices', 'recentQuotes',
            'onboarding', 'onboardingDone', 'onboardingTotal', 'showOnboarding'
        ));
    }

    public function dismissOnboarding(): \Illuminate\Http\RedirectResponse
    {
        session(['onboarding_dismissed' => true]);
        return redirect(url('client/dashboard'));
    }

    public function setLanguage(string $locale)
    {
        if (in_array($locale, ['fr', 'en'])) {
            Session::put('locale', $locale);
            Cookie::queue('wbai_lang', $locale, 60 * 24 * 365);
            app()->setLocale($locale);
            config(['app.locale' => $locale]);
        }

        return redirect()->back();
    }
}
