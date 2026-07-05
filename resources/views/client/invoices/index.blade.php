@extends('client.layout')
@section('title', 'Factures')

@section('topbar-right')
    <a href="{{ url('client/invoices/create') }}" class="btn btn-primary">+ Nouvelle facture</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Toutes les factures <span class="badge">{{ $invoices->total() }}</span></h2>
    </div>

    @if($invoices->isEmpty())
        <div class="empty">
            <div class="empty-icon">🧾</div>
            <p>Aucune facture pour le moment</p>
            <a href="{{ url('client/invoices/create') }}" class="btn btn-primary">+ Créer une facture</a>
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Client</th>
                    <th>Montant</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $invoice)
                <tr>
                    <td style="font-weight:600;">{{ $invoice->number }}</td>
                    <td>{{ $invoice->contact->name ?? '-' }}</td>
                    <td>{{ number_format($invoice->total, 2, ',', ' ') }} {{ $business->currency ?? 'XAF' }}</td>
                    <td><span class="status {{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span></td>
                    <td>{{ \Carbon\Carbon::parse($invoice->issue_date)->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ url('client/invoices/' . $invoice->id) }}" class="btn btn-ghost btn-sm">👁️</a>
                        <form action="{{ url('client/invoices/' . $invoice->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Supprimer cette facture ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--red);">🗑️</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-top:16px;">{{ $invoices->links() }}</div>
    @endif
</div>
@endsection
