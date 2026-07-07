<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Quote;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class DocumentService
{
    /**
     * Génère le PDF d'une facture et retourne son chemin
     */
    public function generateInvoicePdf(Invoice $invoice): string
    {
        $invoice->load(['business', 'contact', 'items']);

        $pdf  = Pdf::loadView('pdf.invoice', compact('invoice'))
                   ->setPaper('a4', 'portrait');

        $path = "invoices/{$invoice->number}.pdf";
        Storage::disk('public')->put($path, $pdf->output());

        $invoice->update(['pdf_path' => $path]);

        return $path;
    }

    /**
     * Génère le PDF d'un devis et retourne son chemin
     */
    public function generateQuotePdf(Quote $quote): string
    {
        $quote->load(['business', 'contact', 'items']);

        $pdf  = Pdf::loadView('pdf.quote', compact('quote'))
                   ->setPaper('a4', 'portrait');

        $path = "quotes/{$quote->number}.pdf";
        Storage::disk('public')->put($path, $pdf->output());

        $quote->update(['pdf_path' => $path]);

        return $path;
    }

    /**
     * Retourne l'URL publique d'un document
     */
    public function getPublicUrl(string $storagePath): string
    {
        return Storage::disk('public')->url($storagePath);
    }

    /**
     * Génère le prochain numéro de facture pour un business
     */
    public function nextInvoiceNumber(\App\Models\Business $business): string
    {
        $year  = now()->format('Y');
        $count = Invoice::where('business_id', $business->id)
                        ->whereYear('created_at', $year)
                        ->count() + 1;

        return sprintf('%s-%s-%04d', $business->invoice_prefix, $year, $count);
    }

    /**
     * Génère le prochain numéro de devis pour un business
     */
    public function nextQuoteNumber(\App\Models\Business $business): string
    {
        $year  = now()->format('Y');
        $count = Quote::where('business_id', $business->id)
                      ->whereYear('created_at', $year)
                      ->count() + 1;

        return sprintf('%s-%s-%04d', $business->quote_prefix, $year, $count);
    }
}
