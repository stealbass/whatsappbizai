<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Contact;
use App\Models\Service;
use App\Services\DocumentService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class InvoiceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $invoices = Invoice::where('business_id', $user->business_id)
            ->with('contact')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('client.invoices.index', compact('user', 'invoices'));
    }

    public function create()
    {
        $user = Auth::user();
        $contacts = Contact::where('business_id', $user->business_id)->orderBy('name')->get();
        $services = Service::where('business_id', $user->business_id)->where('is_active', true)->orderBy('name')->get();

        return view('client.invoices.create', compact('user', 'contacts', 'services'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'contact_id'  => 'required|exists:contacts,id',
            'issue_date'  => 'required|date',
            'due_date'    => 'nullable|date|after:issue_date',
            'tax_rate'    => 'nullable|numeric|min:0|max:100',
            'discount'    => 'nullable|numeric|min:0',
            'notes'       => 'nullable|string|max:2000',
            'items'       => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity'    => 'required|numeric|min:0.01',
            'items.*.unit_price'  => 'required|numeric|min:0',
        ]);

        $contact = Contact::where('id', $data['contact_id'])
            ->where('business_id', $user->business_id)
            ->firstOrFail();

        $business = $user->business;
        $prefix = $business->invoice_prefix ?? 'INV';
        $lastInvoice = Invoice::where('business_id', $user->business_id)->count();
        $number = $prefix . '-' . str_pad($lastInvoice + 1, 4, '0', STR_PAD_LEFT);

        $subtotal = 0;
        foreach ($data['items'] as $item) {
            $subtotal += $item['quantity'] * $item['unit_price'];
        }

        $taxRate = $data['tax_rate'] ?? 0;
        $discount = $data['discount'] ?? 0;
        $taxAmount = $subtotal * ($taxRate / 100);
        $total = $subtotal + $taxAmount - $discount;

        $invoice = Invoice::create([
            'business_id' => $user->business_id,
            'contact_id'  => $contact->id,
            'number'      => $number,
            'status'      => 'draft',
            'issue_date'  => $data['issue_date'],
            'due_date'    => $data['due_date'] ?? null,
            'subtotal'    => $subtotal,
            'tax_rate'    => $taxRate,
            'tax_amount'  => $taxAmount,
            'discount'    => $discount,
            'total'       => $total,
            'notes'       => $data['notes'] ?? null,
            'currency'    => $business->currency ?? 'XAF',
        ]);

        foreach ($data['items'] as $item) {
            InvoiceItem::create([
                'invoice_id'  => $invoice->id,
                'description' => $item['description'],
                'quantity'    => $item['quantity'],
                'unit_price'  => $item['unit_price'],
                'total'       => $item['quantity'] * $item['unit_price'],
            ]);
        }

        return redirect(url('client/invoices/' . $invoice->id))->with('success', 'Facture créée.');
    }

    public function show(Invoice $invoice)
    {
        $user = Auth::user();
        abort_unless($invoice->business_id === $user->business_id, 403);
        $invoice->load(['contact', 'items']);
        $business = $user->business;
        return view('client.invoices.show', compact('user', 'invoice', 'business'));
    }

    public function markPaid(Invoice $invoice)
    {
        $user = Auth::user();
        abort_unless($invoice->business_id === $user->business_id, 403);

        $invoice->update([
            'status'      => 'paid',
            'paid_amount' => $invoice->total,
            'paid_at'     => now(),
        ]);

        return back()->with('success', 'Facture marquée comme payée.');
    }

    public function sendReminder(Invoice $invoice)
    {
        $user = Auth::user();
        abort_unless($invoice->business_id === $user->business_id, 403);

        $business = $user->business;

        if (!$business->whatsapp_phone_number_id || !$business->whatsapp_access_token) {
            return back()->with('error', 'WhatsApp non configuré.');
        }

        if (!$invoice->contact || !$invoice->contact->whatsapp_number) {
            return back()->with('error', 'Pas de numéro WhatsApp pour ce contact.');
        }

        $message = "Bonjour {$invoice->contact->name},\n\n"
            . "Nous vous rappelons que la facture {$invoice->number} d'un montant de "
            . number_format($invoice->total, 0, ',', ' ') . " {$invoice->currency} "
            . "est due depuis le " . \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') . ".\n\n"
            . "Merci de procéder au règlement.\n\n"
            . "Cordialement,\n{$business->name}";

        $whatsapp = app(WhatsAppService::class);
        $sent = $whatsapp->sendText(
            $invoice->contact->whatsapp_number,
            $message,
            $business->whatsapp_phone_number_id,
            $business->whatsapp_access_token
        );

        return $sent
            ? back()->with('success', 'Relance envoyée par WhatsApp.')
            : back()->with('error', 'Échec de l\'envoi.');
    }

    public function generatePdf(Invoice $invoice, DocumentService $docs)
    {
        $user = Auth::user();
        abort_unless($invoice->business_id === $user->business_id, 403);

        $path = $docs->generateInvoicePdf($invoice);
        $url = $docs->getPublicUrl($path);

        return response()->download(public_path($url), "{$invoice->number}.pdf");
    }

    public function sendWhatsApp(Invoice $invoice)
    {
        $user = Auth::user();
        abort_unless($invoice->business_id === $user->business_id, 403);

        $business = $user->business;

        if (!$business->whatsapp_phone_number_id || !$business->whatsapp_access_token) {
            return back()->with('error', 'WhatsApp non configuré.');
        }

        if (!$invoice->contact || !$invoice->contact->whatsapp_number) {
            return back()->with('error', 'Pas de numéro WhatsApp pour ce contact.');
        }

        $docs = app(DocumentService::class);
        $path = $docs->generateInvoicePdf($invoice);
        $url = $docs->getPublicUrl($path);

        $whatsapp = app(WhatsAppService::class);
        $sent = $whatsapp->sendDocument(
            $invoice->contact->whatsapp_number,
            $url,
            "{$invoice->number}.pdf",
            "Voici la facture {$invoice->number} d'un montant de " . number_format($invoice->total, 0, ',', ' ') . " {$invoice->currency}.",
            $business->whatsapp_phone_number_id,
            $business->whatsapp_access_token
        );

        $invoice->update(['status' => 'sent']);

        return $sent
            ? back()->with('success', 'Facture envoyée par WhatsApp.')
            : back()->with('error', 'Échec de l\'envoi.');
    }

    public function destroy(Invoice $invoice)
    {
        $user = Auth::user();
        abort_unless($invoice->business_id === $user->business_id, 403);
        $invoice->items()->delete();
        $invoice->delete();
        return redirect(url('client/invoices'))->with('success', 'Facture supprimée.');
    }
}
