<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('app.client.portal.title') }} — {{ $business->name }}</title>
    <meta name="robots" content="noindex, nofollow">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:#f1f5f9;color:#0f172a;min-height:100vh}
        header{background:#0f172a;padding:20px 32px;display:flex;justify-content:space-between;align-items:center}
        .logo{font-size:18px;font-weight:800;color:#0ea5e9}
        .welcome{color:#94a3b8;font-size:13px}
        .container{max-width:900px;margin:0 auto;padding:32px 20px}
        h2{font-size:18px;font-weight:700;margin:32px 0 16px;color:#0f172a}
        .cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px;margin-bottom:16px}
        .stat{background:#fff;border-radius:12px;padding:20px;border:1px solid #e2e8f0}
        .stat-label{font-size:12px;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px}
        .stat-value{font-size:26px;font-weight:800;color:#0f172a}
        table{width:100%;background:#fff;border-radius:12px;border:1px solid #e2e8f0;border-collapse:collapse;overflow:hidden;margin-bottom:32px}
        th{background:#f8fafc;padding:12px 16px;text-align:left;font-size:12px;font-weight:700;text-transform:uppercase;color:#64748b;border-bottom:1px solid #e2e8f0}
        td{padding:12px 16px;font-size:14px;border-bottom:1px solid #f1f5f9}
        tr:last-child td{border-bottom:none}
        .badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700}
        .badge-paid{background:#dcfce7;color:#166534}
        .badge-sent{background:#dbeafe;color:#1e40af}
        .badge-overdue{background:#fee2e2;color:#991b1b}
        .badge-accepted{background:#dcfce7;color:#166534}
        .badge-pending{background:#fef3c7;color:#92400e}
        .badge-declined{background:#fee2e2;color:#991b1b}
        .btn-sm{display:inline-block;padding:6px 14px;border-radius:6px;font-size:12px;font-weight:700;text-decoration:none;border:none;cursor:pointer}
        .btn-dl{background:#0ea5e9;color:#fff}
        .btn-accept{background:#22c55e;color:#fff}
        .btn-decline{background:#ef4444;color:#fff}
        .alert-success{background:#dcfce7;border:1px solid #86efac;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:14px;color:#166534}
        .alert-info{background:#dbeafe;border:1px solid #93c5fd;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:14px;color:#1e40af}
        .empty{color:#94a3b8;font-size:14px;padding:20px;text-align:center}
    </style>
</head>
<body>

<header>
    <div class="logo">🟢 {{ $business->name }}</div>
    <div class="welcome">{{ __('app.client.portal.welcome') }}, {{ $contact->name }}</div>
</header>

<div class="container">

    @if(session('success'))
        <div class="alert-success">✅ {{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="alert-info">ℹ️ {{ session('info') }}</div>
    @endif

    {{-- Résumé --}}
    <div class="cards">
        <div class="stat">
            <div class="stat-label">{{ __('app.client.portal.total_invoiced') }}</div>
            <div class="stat-value">{{ number_format($invoices->whereNotIn('status',['draft','cancelled'])->sum('total'), 0, ',', ' ') }} {{ $business->currency }}</div>
        </div>
        <div class="stat">
            <div class="stat-label">{{ __('app.client.portal.total_paid') }}</div>
            <div class="stat-value">{{ number_format($invoices->where('status','paid')->sum('paid_amount'), 0, ',', ' ') }} {{ $business->currency }}</div>
        </div>
        <div class="stat">
            <div class="stat-label">{{ __('app.client.portal.pending_quotes') }}</div>
            <div class="stat-value">{{ $quotes->where('status','sent')->count() }}</div>
        </div>
    </div>

    {{-- Factures --}}
    <h2>📄 {{ __('app.client.portal.my_invoices') }}</h2>
    @if($invoices->isEmpty())
        <div class="empty">{{ __('app.client.portal.no_invoices') }}</div>
    @else
    <table>
        <thead>
            <tr>
                <th>{{ __('app.client.portal.number') }}</th>
                <th>Date</th>
                <th>{{ __('app.client.portal.amount') }}</th>
                <th>{{ __('app.client.portal.status') }}</th>
                <th>{{ __('app.client.portal.action') }}</th>
            </tr>
        </thead>
        <tbody>
        @foreach($invoices as $inv)
            <tr>
                <td><strong>{{ $inv->number }}</strong></td>
                <td>{{ $inv->issue_date?->format('d/m/Y') }}</td>
                <td>{{ number_format($inv->total, 0, ',', ' ') }} {{ $inv->currency }}</td>
                <td>
                    @php $cls = ['paid'=>'badge-paid','sent'=>'badge-sent','overdue'=>'badge-overdue'][$inv->status] ?? 'badge-pending' @endphp
                    @php
                    $invoiceStatuses = [
                        'paid' => __('app.client.portal.status_paid'),
                        'sent' => __('app.client.portal.status_sent'),
                        'overdue' => __('app.client.portal.status_overdue'),
                        'draft' => __('app.client.portal.status_draft'),
                    ];
                    @endphp
                    <span class="badge {{ $cls }}">{{ $invoiceStatuses[$inv->status] ?? $inv->status }}</span>
                </td>
                <td>
                    <a href="{{ route('client.invoice.download', [$token, $inv]) }}" class="btn-sm btn-dl">⬇ PDF</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @endif

    {{-- Devis --}}
    <h2>📋 {{ __('app.client.portal.my_quotes') }}</h2>
    @if($quotes->isEmpty())
        <div class="empty">{{ __('app.client.portal.no_quotes') }}</div>
    @else
    <table>
        <thead>
            <tr>
                <th>{{ __('app.client.portal.number') }}</th>
                <th>Date</th>
                <th>{{ __('app.client.portal.amount') }}</th>
                <th>{{ __('app.client.portal.status') }}</th>
                <th>{{ __('app.client.portal.actions') }}</th>
            </tr>
        </thead>
        <tbody>
        @foreach($quotes as $q)
            <tr>
                <td><strong>{{ $q->number }}</strong></td>
                <td>{{ $q->created_at->format('d/m/Y') }}</td>
                <td>{{ number_format($q->total, 0, ',', ' ') }} {{ $q->currency ?? $business->currency }}</td>
                <td>
                    @php $cls = ['accepted'=>'badge-accepted','sent'=>'badge-sent','declined'=>'badge-declined','draft'=>'badge-pending'][$q->status] ?? 'badge-pending' @endphp
                    @php
                    $quoteStatuses = [
                        'accepted' => __('app.client.portal.status_accepted'),
                        'sent' => __('app.client.portal.status_pending'),
                        'declined' => __('app.client.portal.status_declined'),
                        'draft' => __('app.client.portal.status_draft'),
                    ];
                    @endphp
                    <span class="badge {{ $cls }}">{{ $quoteStatuses[$q->status] ?? $q->status }}</span>
                </td>
                <td style="display:flex;gap:6px;flex-wrap:wrap">
                    <a href="{{ route('client.quote.download', [$token, $q]) }}" class="btn-sm btn-dl">⬇ PDF</a>
                    @if($q->status === 'sent')
                    <form method="POST" action="{{ route('client.quote.accept', [$token, $q]) }}" style="display:inline">
                        @csrf
                        <button type="submit" class="btn-sm btn-accept">{{ __('app.client.portal.accept') }}</button>
                    </form>
                    <form method="POST" action="{{ route('client.quote.decline', [$token, $q]) }}" style="display:inline">
                        @csrf
                        <button type="submit" class="btn-sm btn-decline">{{ __('app.client.portal.decline') }}</button>
                    </form>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @endif

</div>

</body>
</html>
