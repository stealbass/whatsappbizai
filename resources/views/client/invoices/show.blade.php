@extends('client.layout')
@section('title', 'Facture ' . $invoice->number)

@section('content')
<div class="card" style="max-width:800px;">
    <div class="card-header">
        <h2>Facture {{ $invoice->number }}</h2>
        <span class="status {{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
        <div>
            <p style="font-size:12px;color:var(--gray);margin-bottom:4px;">Client</p>
            <p style="font-weight:600;">{{ $invoice->contact->name ?? '-' }}</p>
            <p style="font-size:13px;color:var(--gray);">{{ $invoice->contact->email ?? '' }}</p>
            <p style="font-size:13px;color:var(--gray);">{{ $invoice->contact->whatsapp_number ?? '' }}</p>
        </div>
        <div style="text-align:right;">
            <p style="font-size:12px;color:var(--gray);margin-bottom:4px;">Date d'émission</p>
            <p style="font-weight:600;">{{ \Carbon\Carbon::parse($invoice->issue_date)->format('d/m/Y') }}</p>
            <p style="font-size:12px;color:var(--gray);margin-bottom:4px;margin-top:8px;">Échéance</p>
            <p style="font-weight:600;">{{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') : '-' }}</p>
        </div>
    </div>

    @if($invoice->notes)
        <div style="margin-bottom:20px;">
            <p style="font-size:12px;color:var(--gray);margin-bottom:4px;">Notes</p>
            <p style="font-size:14px;">{{ $invoice->notes }}</p>
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th style="text-align:center;">Qté</th>
                <th style="text-align:right;">Prix unitaire</th>
                <th style="text-align:right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td style="text-align:center;">{{ $item->quantity }}</td>
                <td style="text-align:right;">{{ number_format($item->unit_price, 0, ',', ' ') }}</td>
                <td style="text-align:right;font-weight:600;">{{ number_format($item->total, 0, ',', ' ') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align:right;font-weight:600;">Sous-total</td>
                <td style="text-align:right;">{{ number_format($invoice->subtotal, 0, ',', ' ') }} {{ $invoice->currency }}</td>
            </tr>
            @if($invoice->tax_rate > 0)
            <tr>
                <td colspan="3" style="text-align:right;">TVA ({{ $invoice->tax_rate }}%)</td>
                <td style="text-align:right;">{{ number_format($invoice->tax_amount, 0, ',', ' ') }} {{ $invoice->currency }}</td>
            </tr>
            @endif
            @if($invoice->discount > 0)
            <tr>
                <td colspan="3" style="text-align:right;color:var(--red);">Remise</td>
                <td style="text-align:right;color:var(--red);">-{{ number_format($invoice->discount, 0, ',', ' ') }} {{ $invoice->currency }}</td>
            </tr>
            @endif
            <tr>
                <td colspan="3" style="text-align:right;font-weight:700;border-top:2px solid var(--border);">Total</td>
                <td style="text-align:right;font-weight:700;font-size:16px;border-top:2px solid var(--border);">{{ number_format($invoice->total, 0, ',', ' ') }} {{ $invoice->currency }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="display:flex;flex-wrap:wrap;gap:10px;margin-top:24px;">
        <a href="{{ url('client/invoices') }}" class="btn btn-outline">← Retour</a>

        @if(!in_array($invoice->status, ['paid', 'cancelled']))
            <form action="{{ url('client/invoices/' . $invoice->id . '/mark-paid') }}" method="POST" onsubmit="return confirm('Marquer cette facture comme payée ?')">
                @csrf
                <button type="submit" class="btn btn-primary">✅ Marquer payée</button>
            </form>
        @endif

        @if($invoice->contact && $invoice->contact->whatsapp_number && !in_array($invoice->status, ['paid']))
            @if($business->whatsapp_phone_number_id)
                <form action="{{ url('client/invoices/' . $invoice->id . '/reminder') }}" method="POST" onsubmit="return confirm('Envoyer une relance WhatsApp ?')">
                    @csrf
                    <button type="submit" class="btn btn-outline">🔔 Relance WhatsApp</button>
                </form>
                <form action="{{ url('client/invoices/' . $invoice->id . '/whatsapp') }}" method="POST" onsubmit="return confirm('Envoyer la facture par WhatsApp ?')">
                    @csrf
                    <button type="submit" class="btn btn-outline">📲 Envoyer WhatsApp</button>
                </form>
            @endif
        @endif

        <a href="{{ url('client/invoices/' . $invoice->id . '/pdf') }}" class="btn btn-outline" target="_blank">📥 PDF</a>

        @if(!in_array($invoice->status, ['paid', 'cancelled']))
            <form action="{{ url('client/invoices/' . $invoice->id) }}" method="POST" style="margin-left:auto;" onsubmit="return confirm('Supprimer cette facture ?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">Supprimer</button>
            </form>
        @endif
    </div>
</div>
@endsection
