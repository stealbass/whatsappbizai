<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Invoice;
use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Espace Client — Portail en libre-service pour les contacts
 * URL: /client/{token}
 */
class ClientPortalController extends Controller
{
    /**
     * Affiche le portail client via un token unique
     */
    public function show(string $token)
    {
        $contact = Contact::where('portal_token', $token)->firstOrFail();
        $business = $contact->business;

        $invoices = Invoice::where('contact_id', $contact->id)
            ->whereNotIn('status', ['draft'])
            ->orderByDesc('issue_date')
            ->get();

        $quotes = Quote::where('contact_id', $contact->id)
            ->whereNotIn('status', ['draft'])
            ->orderByDesc('created_at')
            ->get();

        return view('client.portal', compact('contact', 'business', 'invoices', 'quotes', 'token'));
    }

    /**
     * Télécharge un PDF facture (accessible sans login si token valide)
     */
    public function downloadInvoice(string $token, Invoice $invoice)
    {
        $contact = Contact::where('portal_token', $token)->firstOrFail();
        abort_unless($invoice->contact_id === $contact->id, 403);

        $docs = app(\App\Services\DocumentService::class);
        $path = $docs->generateInvoicePdf($invoice);

        return response()->download(storage_path('app/' . $path));
    }

    /**
     * Télécharge un PDF devis
     */
    public function downloadQuote(string $token, Quote $quote)
    {
        $contact = Contact::where('portal_token', $token)->firstOrFail();
        abort_unless($quote->contact_id === $contact->id, 403);

        $docs = app(\App\Services\DocumentService::class);
        $path = $docs->generateQuotePdf($quote);

        return response()->download(storage_path('app/' . $path));
    }

    /**
     * Accepte un devis depuis le portail
     */
    public function acceptQuote(string $token, Quote $quote)
    {
        $contact = Contact::where('portal_token', $token)->firstOrFail();
        abort_unless($quote->contact_id === $contact->id, 403);

        $quote->update(['status' => 'accepted']);

        return back()->with('success', 'Devis accepté ! Nous vous contacterons rapidement.');
    }

    /**
     * Refuse un devis depuis le portail
     */
    public function declineQuote(string $token, Quote $quote)
    {
        $contact = Contact::where('portal_token', $token)->firstOrFail();
        abort_unless($quote->contact_id === $contact->id, 403);

        $quote->update(['status' => 'declined']);

        return back()->with('info', 'Devis refusé. Merci de nous avoir informés.');
    }
}
