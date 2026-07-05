<?php

namespace App\Observers;

use App\Models\InvoiceItem;
use App\Models\Invoice;

class InvoiceItemObserver
{
    public function saved(InvoiceItem $item): void
    {
        // Recalcule le total de la ligne
        $total = round($item->quantity * $item->unit_price, 2);
        if ($item->total != $total) {
            $item->withoutEvents(fn () => $item->update(['total' => $total]));
        }

        // Recalcule les totaux de la facture
        $this->recalculateInvoice($item->invoice_id);
    }

    public function deleted(InvoiceItem $item): void
    {
        $this->recalculateInvoice($item->invoice_id);
    }

    private function recalculateInvoice(int $invoiceId): void
    {
        $invoice  = Invoice::withoutGlobalScopes()->find($invoiceId);
        if (!$invoice) return;

        $subtotal = InvoiceItem::where('invoice_id', $invoiceId)->sum('total');
        $tax      = round($subtotal * ($invoice->tax_rate / 100), 2);
        $total    = round($subtotal + $tax - $invoice->discount, 2);

        $invoice->withoutEvents(fn () => $invoice->update([
            'subtotal'   => $subtotal,
            'tax_amount' => $tax,
            'total'      => $total,
        ]));
    }
}
