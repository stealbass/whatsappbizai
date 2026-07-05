<?php

namespace App\Observers;

use App\Models\QuoteItem;
use App\Models\Quote;

class QuoteItemObserver
{
    public function saved(QuoteItem $item): void
    {
        $total = round($item->quantity * $item->unit_price, 2);
        if ($item->total != $total) {
            $item->withoutEvents(fn () => $item->update(['total' => $total]));
        }
        $this->recalculateQuote($item->quote_id);
    }

    public function deleted(QuoteItem $item): void
    {
        $this->recalculateQuote($item->quote_id);
    }

    private function recalculateQuote(int $quoteId): void
    {
        $quote = Quote::withoutGlobalScopes()->find($quoteId);
        if (!$quote) return;

        $subtotal = QuoteItem::where('quote_id', $quoteId)->sum('total');
        $tax      = round($subtotal * ($quote->tax_rate / 100), 2);
        $total    = round($subtotal + $tax - $quote->discount, 2);

        $quote->withoutEvents(fn () => $quote->update([
            'subtotal'   => $subtotal,
            'tax_amount' => $tax,
            'total'      => $total,
        ]));
    }
}
