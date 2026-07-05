@extends('client.layout')
@section('title', __('app.client.dashboard.title'))

@section('content')
<div class="welcome" style="margin-bottom:24px;">
    <h1 style="font-size:24px;font-weight:800;">{{ __('app.client.dashboard.welcome') }}, {{ explode(' ', $user->name)[0] }} 👋</h1>
    <p style="color:var(--gray);font-size:14px;">{{ __('app.client.dashboard.summary') }}</p>
</div>

@if(!$business || !$business->whatsapp_phone_number_id)
<div class="alert alert-warning" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
    <div>
        <strong>📱 {{ __('app.client.dashboard.setup_title') }}</strong><br>
        <span style="font-size:13px;">{{ __('app.client.dashboard.setup_desc') }}</span>
    </div>
    <a href="{{ url('client/settings/whatsapp') }}" class="btn btn-primary btn-sm">{{ __('app.client.dashboard.setup_btn') }}</a>
</div>
@endif

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-value">{{ number_format($stats['contacts']) }}</div>
        <div class="stat-label">{{ __('app.client.dashboard.contacts') }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🧾</div>
        <div class="stat-value">{{ number_format($stats['invoices']) }}</div>
        <div class="stat-label">{{ __('app.client.dashboard.invoices') }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📄</div>
        <div class="stat-value">{{ number_format($stats['quotes']) }}</div>
        <div class="stat-label">{{ __('app.client.dashboard.quotes') }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">💬</div>
        <div class="stat-value">{{ number_format($stats['conversations']) }}</div>
        <div class="stat-label">{{ __('app.client.dashboard.conversations') }}</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
    <div class="card">
        <div class="card-header">
            <h2>🧾 {{ __('app.client.dashboard.recent_invoices') }}</h2>
            <a href="{{ url('client/invoices') }}" class="btn btn-ghost btn-sm">{{ __('app.client.dashboard.view_all') }}</a>
        </div>
        @if($recentInvoices->isEmpty())
            <div class="empty">
                <div class="empty-icon">🧾</div>
                <p>{{ __('app.client.dashboard.no_invoices') }}</p>
                <a href="{{ url('client/invoices/create') }}" class="btn btn-primary btn-sm">{{ __('app.client.dashboard.create_invoice') }}</a>
            </div>
        @else
            <table>
                <thead><tr><th>{{ __('app.client.invoices.number') }}</th><th>{{ __('app.client.invoices.client') }}</th><th>{{ __('app.client.invoices.amount') }}</th><th>{{ __('app.client.invoices.status') }}</th></tr></thead>
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
            <h2>📄 {{ __('app.client.dashboard.recent_quotes') }}</h2>
            <a href="{{ url('client/quotes') }}" class="btn btn-ghost btn-sm">{{ __('app.client.dashboard.view_all') }}</a>
        </div>
        @if($recentQuotes->isEmpty())
            <div class="empty">
                <div class="empty-icon">📄</div>
                <p>{{ __('app.client.dashboard.no_quotes') }}</p>
                <a href="{{ url('client/quotes/create') }}" class="btn btn-primary btn-sm">{{ __('app.client.dashboard.create_quote') }}</a>
            </div>
        @else
            <table>
                <thead><tr><th>{{ __('app.client.quotes.number') }}</th><th>{{ __('app.client.quotes.client') }}</th><th>{{ __('app.client.quotes.amount') }}</th><th>{{ __('app.client.quotes.status') }}</th></tr></thead>
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
