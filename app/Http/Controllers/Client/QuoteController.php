<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Contact;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $prefix = $business->quote_prefix ?? 'DEV';
        $lastQuote = Quote::where('business_id', $user->business_id)->count();
        $number = $prefix . '-' . str_pad($lastQuote + 1, 4, '0', STR_PAD_LEFT);

        $subtotal = 0;
        foreach ($data['items'] as $item) {
            $subtotal += $item['quantity'] * $item['unit_price'];
        }

        $quote = Quote::create([
            'business_id' => $user->business_id,
            'contact_id'  => $contact->id,
            'number'      => $number,
            'status'      => 'draft',
            'valid_until' => $data['valid_until'] ?? null,
            'subtotal'    => $subtotal,
            'tax_rate'    => 0,
            'tax_amount'  => 0,
            'total'       => $subtotal,
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

        return redirect(url('client/quotes/' . $quote->id))->with('success', 'Devis créé.');
    }

    public function show(Quote $quote)
    {
        $user = Auth::user();
        abort_unless($quote->business_id === $user->business_id, 403);
        $quote->load(['contact', 'items']);
        return view('client.quotes.show', compact('user', 'quote'));
    }

    public function destroy(Quote $quote)
    {
        $user = Auth::user();
        abort_unless($quote->business_id === $user->business_id, 403);
        $quote->items()->delete();
        $quote->delete();
        return redirect(url('client/quotes'))->with('success', 'Devis supprimé.');
    }
}
