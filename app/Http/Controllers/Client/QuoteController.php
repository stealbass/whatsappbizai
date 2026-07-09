<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Contact;
use App\Models\Service;
use App\Services\DocumentService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class QuoteController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $quotes = Quote::where('business_id', $user->business_id)
            ->with('contact')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('client.quotes.index', compact('user', 'quotes'));
    }

    public function create()
    {
        $user = Auth::user();
        $contacts = Contact::where('business_id', $user->business_id)->orderBy('name')->get();
        $services = Service::where('business_id', $user->business_id)->where('is_active', true)->orderBy('name')->get();

        return view('client.quotes.create', compact('user', 'contacts', 'services'));
    }


    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'contact_id'  => 'required|exists:contacts,id',
            'valid_until' => 'nullable|date|after:today',
            'tax_rate'    => 'nullable|numeric|min:0|max:100',
            'discount'    => 'nullable|numeric|min:0',
            'notes'       => 'nullable|string|max:100000',
            'items'       => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity'    => 'required|numeric|min:0.01',
            'items.*.unit_price'  => 'required|numeric|min:0',
        ]);

        $contact = Contact::where('id', $data['contact_id'])
            ->where('business_id', $user->business_id)
            ->firstOrFail();

        $business = $user->business;
        $prefix = $business->quote_prefix ?? 'DEV';
        $lastQuote = Quote::where('business_id', $user->business_id)->count();
        $number = $prefix . '-' . str_pad($lastQuote + 1, 4, '0', STR_PAD_LEFT);

        $subtotal = 0;
        foreach ($data['items'] as $item) {
            $subtotal += $item['quantity'] * $item['unit_price'];
        }

        $taxRate = $data['tax_rate'] ?? 0;
        $discount = $data['discount'] ?? 0;
        $taxAmount = $subtotal * ($taxRate / 100);
        $total = $subtotal + $taxAmount - $discount;

        $quote = Quote::create([
            'business_id' => $user->business_id,
            'contact_id'  => $contact->id,
            'number'      => $number,
            'status'      => 'draft',
            'valid_until' => $data['valid_until'] ?? null,
            'subtotal'    => $subtotal,
            'tax_rate'    => $taxRate,
            'tax_amount'  => $taxAmount,
            'discount'    => $discount,
            'total'       => $total,
            'notes'       => $data['notes'] ?? null,
            'currency'    => $business->currency ?? 'XAF',
        ]);

        foreach ($data['items'] as $item) {
            QuoteItem::create([
                'quote_id'    => $quote->id,
                'description' => $item['description'],
                'quantity'    => $item['quantity'],
                'unit_price'  => $item['unit_price'],
                'total'       => $item['quantity'] * $item['unit_price'],
            ]);
        }

        return redirect(url('client/quotes/' . $quote->id))->with('success', __('app.client.flash.quote_created'));
    }

    public function edit(Quote $quote)
    {
        $user = Auth::user();
        abort_unless($quote->business_id === $user->business_id, 403);
        abort_unless(in_array($quote->status, ['draft', 'sent']), 403);
        $quote->load(['contact', 'items']);
        $contacts = Contact::where('business_id', $user->business_id)->orderBy('name')->get();
        $services = Service::where('business_id', $user->business_id)->where('is_active', true)->orderBy('name')->get();
        return view('client.quotes.edit', compact('user', 'quote', 'contacts', 'services'));
    }

    public function update(Request $request, Quote $quote)
    {
        $user = Auth::user();
        abort_unless($quote->business_id === $user->business_id, 403);
        abort_unless(in_array($quote->status, ['draft', 'sent']), 403);

        $data = $request->validate([
            'contact_id'  => 'required|exists:contacts,id',
            'valid_until' => 'nullable|date',
            'tax_rate'    => 'nullable|numeric|min:0|max:100',
            'discount'    => 'nullable|numeric|min:0',
            'notes'       => 'nullable|string|max:100000',
            'items'       => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity'    => 'required|numeric|min:0.01',
            'items.*.unit_price'  => 'required|numeric|min:0',
        ]);

        $subtotal = 0;
        foreach ($data['items'] as $item) {
            $subtotal += $item['quantity'] * $item['unit_price'];
        }
        $taxRate   = $data['tax_rate'] ?? 0;
        $discount  = $data['discount'] ?? 0;
        $taxAmount = $subtotal * ($taxRate / 100);
        $total     = $subtotal + $taxAmount - $discount;

        $quote->update([
            'contact_id'  => $data['contact_id'],
            'valid_until' => $data['valid_until'] ?? null,
            'subtotal'    => $subtotal,
            'tax_rate'    => $taxRate,
            'tax_amount'  => $taxAmount,
            'discount'    => $discount,
            'total'       => $total,
            'notes'       => $data['notes'] ?? null,
        ]);

        $quote->items()->delete();
        foreach ($data['items'] as $item) {
            \App\Models\QuoteItem::create([
                'quote_id'    => $quote->id,
                'description' => $item['description'],
                'quantity'    => $item['quantity'],
                'unit_price'  => $item['unit_price'],
                'total'       => $item['quantity'] * $item['unit_price'],
            ]);
        }

        return redirect(url('client/quotes/' . $quote->id))->with('success', __('app.client.flash.quote_updated'));
    }

    public function generatePdf(Quote $quote, DocumentService $docs)
    {
        $user = Auth::user();
        abort_unless($quote->business_id === $user->business_id, 403);

        $path = $docs->generateQuotePdf($quote);
        $fullPath = storage_path('app/public/' . $path);

        return response()->download($fullPath, "{$quote->number}.pdf");
    }

    public function sendWhatsApp(Quote $quote)
    {
        $user = Auth::user();
        abort_unless($quote->business_id === $user->business_id, 403);

        $business = $user->business;

        if (!$quote->contact || !$quote->contact->whatsapp_number) {
            return back()->with('error', __('app.client.flash.no_whatsapp_number'));
        }

        $docs = app(DocumentService::class);
        $path = $docs->generateQuotePdf($quote);
        $url = $docs->getPublicUrl($path);

        $whatsapp = app(WhatsAppService::class);
        $sent = $whatsapp->sendDocument(
            $quote->contact->whatsapp_number,
            $url,
            "{$quote->number}.pdf",
            "Voici le devis {$quote->number} d'un montant de " . number_format($quote->total, 0, ',', ' ') . " {$quote->currency}.",
            $business,
            'quote'
        );

        $quote->update(['status' => 'sent']);

        return $sent
            ? back()->with('success', __('app.client.flash.quote_sent'))
            : back()->with('error', __('app.client.flash.send_failed'));
    }

    public function sendEmail(Quote $quote)
    {
        $user = Auth::user();
        abort_unless($quote->business_id === $user->business_id, 403);

        $business = $user->business;

        if (!$quote->contact || !$quote->contact->email) {
            return back()->with('error', __('app.client.flash.no_email'));
        }

        $docs = app(DocumentService::class);
        $path = $docs->generateQuotePdf($quote);

        Mail::to($quote->contact->email)->send(
            new \App\Mail\DocumentMail(
                $business,
                'quote',
                $quote->number,
                $quote->total,
                $quote->currency,
                $path,
            )
        );

        $quote->update(['status' => 'sent']);

        return back()->with('success', __('app.client.flash.quote_email_sent'));
    }

    public function convertToInvoice(Quote $quote)
    {
        $user = Auth::user();
        abort_unless($quote->business_id === $user->business_id, 403);

        $business = $user->business;
        $prefix = $business->invoice_prefix ?? 'INV';
        $lastInvoice = Invoice::where('business_id', $user->business_id)->count();
        $number = $prefix . '-' . str_pad($lastInvoice + 1, 4, '0', STR_PAD_LEFT);

        $invoice = Invoice::create([
            'business_id' => $user->business_id,
            'contact_id'  => $quote->contact_id,
            'number'      => $number,
            'status'      => 'draft',
            'issue_date'  => now()->toDateString(),
            'due_date'    => now()->addDays(30)->toDateString(),
            'subtotal'    => $quote->subtotal,
            'tax_rate'    => $quote->tax_rate,
            'tax_amount'  => $quote->tax_amount,
            'discount'    => $quote->discount,
            'total'       => $quote->total,
            'notes'       => $quote->notes,
            'currency'    => $quote->currency,
        ]);

        foreach ($quote->items as $item) {
            InvoiceItem::create([
                'invoice_id'  => $invoice->id,
                'description' => $item->description,
                'quantity'    => $item->quantity,
                'unit_price'  => $item->unit_price,
                'total'       => $item->total,
            ]);
        }

        $quote->update(['status' => 'accepted']);

        return redirect(url('client/invoices/' . $invoice->id))
            ->with('success', "Facture {$invoice->number} créée à partir du devis {$quote->number}.");
    }

    public function destroy(Quote $quote)
    {
        $user = Auth::user();
        abort_unless($quote->business_id === $user->business_id, 403);
        $quote->items()->delete();
        $quote->delete();
        return redirect(url('client/quotes'))->with('success', __('app.client.flash.quote_deleted'));
    }
}
