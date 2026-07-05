<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Contact;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'notes'       => 'nullable|string|max:2000',
            'items'       => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity'    => 'required|numeric|min:0.01',
            'items.*.unit_price'  => 'required|numeric|min:0',
        ]);

        // Verify contact belongs to business
        $contact = Contact::where('id', $data['contact_id'])
            ->where('business_id', $user->business_id)
            ->firstOrFail();

        $business = $user->business;
        $prefix = $business->quote_prefix ?? 'INV';
        $lastInvoice = Invoice::where('business_id', $user->business_id)->count();
        $number = $prefix . '-' . str_pad($lastInvoice + 1, 4, '0', STR_PAD_LEFT);

        $subtotal = 0;
        foreach ($data['items'] as $item) {
            $subtotal += $item['quantity'] * $item['unit_price'];
        }

        $invoice = Invoice::create([
            'business_id' => $user->business_id,
            'contact_id'  => $contact->id,
            'number'      => $number,
            'status'      => 'draft',
            'issue_date'  => $data['issue_date'],
            'due_date'    => $data['due_date'] ?? null,
            'subtotal'    => $subtotal,
            'tax_rate'    => 0,
            'tax_amount'  => 0,
            'total'       => $subtotal,
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
        return view('client.invoices.show', compact('user', 'invoice'));
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
