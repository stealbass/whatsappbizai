<?php

namespace App\Actions;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Quote;
use App\Services\DocumentService;

class ConvertQuoteToInvoice
{
    public function __construct(private readonly DocumentService $docs) {}

    /**
     * Convertit un devis accepté en facture
     */
    public function execute(Quote $quote): Invoice
    {
        $business = $quote->business;

        // Crée la facture depuis le devis
        $invoice = Invoice::create([
            'business_id'  => $quote->business_id,
            'contact_id'   => $quote->contact_id,
            'number'       => $this->docs->nextInvoiceNumber($business),
            'status'       => 'sent',
            'issue_date'   => now(),
            'due_date'     => now()->addDays(30),
            'subtotal'     => $quote->subtotal,
            'tax_rate'     => $quote->tax_rate,
            'tax_amount'   => $quote->tax_amount,
            'discount'     => $quote->discount,
            'total'        => $quote->total,
            'paid_amount'  => 0,
            'currency'     => $quote->currency,
            'notes'        => $quote->notes,
        ]);

        // Copie les lignes du devis
        foreach ($quote->items as $item) {
            InvoiceItem::create([
                'invoice_id'  => $invoice->id,
                'description' => $item->description,
                'quantity'    => $item->quantity,
                'unit_price'  => $item->unit_price,
                'total'       => $item->total,
            ]);
        }

        // Marque le devis comme accepté et lié à la facture
        $quote->update([
            'status'                  => 'accepted',
            'converted_to_invoice_id' => $invoice->id,
        ]);

        return $invoice;
    }
}
