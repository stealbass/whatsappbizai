@extends('client.layout')
@section('title', 'Facture ' . $invoice->number)

@section('content')
<div class="card" style="max-width:700px;">
    <div class="card-header">
        <h2>Facture {{ $invoice->number }}</h2>
        <span class="status {{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
        <div>
            <p style="font-size:12px;color:var(--gray);margin-bottom:4px;">Client</p>
            <p style="font-weight:600;">{{ $invoice->contact->name ?? '-' }}</p>
            <p style="font-size:13px;color:var(--gray);">{{ $invoice->contact->email ?? '' }}</p>
        </div>
        <div style="text-align:right;">
            <p style="font-size:12px;color:var(--gray);margin-bottom:4px;">Date d'émission</p>
            <p style="font-weight:600;">{{ \Carbon\Carbon::parse($invoice->issue_date)->format('d/m/Y') }}</p>
            <p style="font-size:12px;color:var(--gray);margin-bottom:4px;margin-top:8px;">Échéance</p>
            <p style="font-weight:600;">{{ \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') }}</p>
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
                <td style="text-align:right;">{{ number_format($item->unit_price, 2, ',', ' ') }}</td>
                <td style="text-align:right;font-weight:600;">{{ number_format($item->quantity * $item->unit_price, 2, ',', ' ') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align:right;font-weight:700;border-top:2px solid var(--border);">Total</td>
                <td style="text-align:right;font-weight:700;font-size:16px;border-top:2px solid var(--border);">{{ number_format($invoice->total, 2, ',', ' ') }} {{ $business->currency ?? 'XAF' }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="display:flex;gap:12px;margin-top:24px;">
        <a href="{{ url('client/invoices') }}" class="btn btn-outline">← Retour</a>
        <form action="{{ url('client/invoices/' . $invoice->id) }}" method="POST" style="margin-left:auto;" onsubmit="return confirm('Supprimer cette facture ?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger">Supprimer</button>
        </form>
    </div>
</div>
@endsection
