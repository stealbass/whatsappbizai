@extends('client.layout')
@section('title', 'Tableau de bord')

@section('content')
<div class="welcome" style="margin-bottom:24px;">
    <h1 style="font-size:24px;font-weight:800;">Bonjour, {{ explode(' ', $user->name)[0] }} 👋</h1>
    <p style="color:var(--gray);font-size:14px;">Voici un résumé de votre activité</p>
</div>

@if(!$business || !$business->whatsapp_phone_number_id)
<div class="alert alert-warning" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
    <div>
        <strong>📱 Configurez votre agent IA</strong><br>
        <span style="font-size:13px;">Connectez votre numéro WhatsApp pour activer l'agent IA automatique.</span>
    </div>
    <a href="{{ url('client/settings/whatsapp') }}" class="btn btn-primary btn-sm">Configurer →</a>
</div>
@endif

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-value">{{ number_format($stats['contacts']) }}</div>
        <div class="stat-label">Contacts</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🧾</div>
        <div class="stat-value">{{ number_format($stats['invoices']) }}</div>
        <div class="stat-label">Factures</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📄</div>
        <div class="stat-value">{{ number_format($stats['quotes']) }}</div>
        <div class="stat-label">Devis</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">💬</div>
        <div class="stat-value">{{ number_format($stats['conversations']) }}</div>
        <div class="stat-label">Conversations IA</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
    <div class="card">
        <div class="card-header">
            <h2>🧾 Factures récentes</h2>
            <a href="{{ url('client/invoices') }}" class="btn btn-ghost btn-sm">Voir tout →</a>
        </div>
        @if($recentInvoices->isEmpty())
            <div class="empty">
                <div class="empty-icon">🧾</div>
                <p>Aucune facture pour le moment</p>
                <a href="{{ url('client/invoices/create') }}" class="btn btn-primary btn-sm">Créer une facture</a>
            </div>
        @else
            <table>
                <thead><tr><th>#</th><th>Client</th><th>Montant</th><th>Statut</th></tr></thead>
                <tbody>
                    @foreach($recentInvoices as $inv)
                    <tr>
                        <td><a href="{{ url('client/invoices/' . $inv->id) }}" style="color:var(--sky);text-decoration:none;font-weight:600;">{{ $inv->number }}</a></td>
                        <td>{{ $inv->contact->name ?? '-' }}</td>
                        <td>{{ number_format($inv->total, 0, ',', ' ') }} {{ $business->currency ?? 'XAF' }}</td>
                        <td><span class="status {{ $inv->status }}">{{ $inv->status }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="card">
        <div class="card-header">
            <h2>📄 Devis récents</h2>
            <a href="{{ url('client/quotes') }}" class="btn btn-ghost btn-sm">Voir tout →</a>
        </div>
        @if($recentQuotes->isEmpty())
            <div class="empty">
                <div class="empty-icon">📄</div>
                <p>Aucun devis pour le moment</p>
                <a href="{{ url('client/quotes/create') }}" class="btn btn-primary btn-sm">Créer un devis</a>
            </div>
        @else
            <table>
                <thead><tr><th>#</th><th>Client</th><th>Montant</th><th>Statut</th></tr></thead>
                <tbody>
                    @foreach($recentQuotes as $q)
                    <tr>
                        <td><a href="{{ url('client/quotes/' . $q->id) }}" style="color:var(--sky);text-decoration:none;font-weight:600;">{{ $q->number }}</a></td>
                        <td>{{ $q->contact->name ?? '-' }}</td>
                        <td>{{ number_format($q->total, 0, ',', ' ') }} {{ $business->currency ?? 'XAF' }}</td>
                        <td><span class="status {{ $q->status }}">{{ $q->status }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
