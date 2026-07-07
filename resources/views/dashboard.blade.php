<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('app.dashboard.title') ?? 'Dashboard' }} — {{ $site->site_name ?? 'WhatsAppBizAI' }}</title>
    <style>
        :root { --sky: #0ea5e9; --sky-dark: #0284c7; --dark: #0f172a; --mid: #1e293b; --gray: #64748b; --light: #f8fafc; --green: #22c55e; --red: #ef4444; --orange: #f59e0b; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f1f5f9; color: var(--dark); min-height: 100vh; }

        .topbar { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 0 24px; display: flex; align-items: center; justify-content: space-between; height: 64px; position: sticky; top: 0; z-index: 50; }
        .topbar .logo { font-size: 18px; font-weight: 800; color: var(--dark); text-decoration: none; }
        .topbar .logo span { color: var(--sky); }
        .topbar-right { display: flex; align-items: center; gap: 16px; }
        .topbar-right .plan-badge { background: #e0f2fe; color: var(--sky-dark); padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; }
        .topbar-right .user-name { font-size: 14px; font-weight: 600; color: var(--dark); }
        .topbar-right .logout { font-size: 13px; color: var(--gray); text-decoration: none; padding: 6px 12px; border-radius: 6px; border: 1px solid #e2e8f0; }
        .topbar-right .logout:hover { background: #f1f5f9; }

        .container { max-width: 1100px; margin: 0 auto; padding: 32px 24px; }

        .welcome { margin-bottom: 32px; }
        .welcome h1 { font-size: 28px; font-weight: 800; margin-bottom: 4px; }
        .welcome p { font-size: 15px; color: var(--gray); }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 32px; }
        .stat-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; }
        .stat-card .stat-icon { font-size: 28px; margin-bottom: 8px; }
        .stat-card .stat-value { font-size: 32px; font-weight: 800; color: var(--dark); }
        .stat-card .stat-label { font-size: 13px; color: var(--gray); margin-top: 2px; }

        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px; }
        .card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; }
        .card h2 { font-size: 17px; font-weight: 700; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
        .card h2 .badge { font-size: 12px; background: #f1f5f9; color: var(--gray); padding: 2px 8px; border-radius: 10px; font-weight: 500; }

        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; font-size: 12px; font-weight: 600; color: var(--gray); text-transform: uppercase; padding: 8px 12px; border-bottom: 1px solid #e2e8f0; }
        td { font-size: 14px; padding: 10px 12px; border-bottom: 1px solid #f1f5f9; }
        .status { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; }
        .status.paid { background: #dcfce7; color: #166534; }
        .status.pending { background: #fef3c7; color: #92400e; }
        .status.overdue { background: #fef2f2; color: #991b1b; }
        .status.accepted { background: #dcfce7; color: #166534; }
        .status.sent { background: #e0f2fe; color: #0369a1; }
        .status.draft { background: #f1f5f9; color: var(--gray); }
        .empty { text-align: center; padding: 24px; color: var(--gray); font-size: 14px; }

        .setup-banner { background: linear-gradient(135deg, #0ea5e9 0%, #0f172a 100%); border-radius: 12px; padding: 24px; margin-bottom: 32px; color: #fff; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; }
        .setup-banner h3 { font-size: 18px; font-weight: 700; }
        .setup-banner p { font-size: 14px; color: #bae6fd; }
        .setup-banner a { background: #fff; color: var(--sky); padding: 10px 20px; border-radius: 8px; font-weight: 700; font-size: 14px; text-decoration: none; }

        @media (max-width: 768px) {
            .grid-2 { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .setup-banner { flex-direction: column; text-align: center; }
        }
    </style>
</head>
<body>

<div class="topbar">
    @php $siteName = $site->site_name ?? 'WhatsAppBizAI'; $parts = explode('BizAI', $siteName); @endphp
    <a href="{{ url('dashboard') }}" class="logo">{!! $parts[0] ?? $siteName !!}<span>{{ str_contains($siteName, 'BizAI') ? 'BizAI' : '' }}</span></a>
    <div class="topbar-right">
        @if(session('previous_user_id'))
            <a href="{{ url('impersonate/' . session('previous_user_id')) }}" style="background:#6366f1;color:#fff;padding:6px 14px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                ← Back to admin
            </a>
        @endif
        <span class="plan-badge">{{ $business ? ucfirst($business->plan) : 'Free' }}</span>
        <span class="user-name">{{ $user->name }}</span>
        <a href="{{ url('logout') }}" class="logout">{{ app()->getLocale() === 'fr' ? 'Déconnexion' : 'Logout' }}</a>
    </div>
</div>

<div class="container">
    <div class="welcome">
        <h1>{{ app()->getLocale() === 'fr' ? 'Bonjour' : 'Welcome' }}, {{ explode(' ', $user->name)[0] }} 👋</h1>
        <p>{{ app()->getLocale() === 'fr' ? 'Voici un résumé de votre activité' : "Here's a summary of your activity" }}</p>
    </div>

    @if(!$business || !$business->whatsapp_phone_number_id)
    <div class="setup-banner">
        <div>
            <h3>{{ app()->getLocale() === 'fr' ? '🔧 Configurez votre agent IA' : '🔧 Set up your AI agent' }}</h3>
            <p>{{ app()->getLocale() === 'fr' ? 'Connectez votre numéro WhatsApp pour activer l\'agent IA, les devis et factures automatiques.' : 'Connect your WhatsApp number to enable the AI agent, automatic quotes and invoices.' }}</p>
        </div>
        <a href="/admin">{{ app()->getLocale() === 'fr' ? 'Configurer maintenant' : 'Set up now' }}</a>
    </div>
    @endif

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-value">{{ number_format($stats['contacts']) }}</div>
            <div class="stat-label">{{ app()->getLocale() === 'fr' ? 'Contacts' : 'Contacts' }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🧾</div>
            <div class="stat-value">{{ number_format($stats['invoices']) }}</div>
            <div class="stat-label">{{ app()->getLocale() === 'fr' ? 'Factures' : 'Invoices' }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📄</div>
            <div class="stat-value">{{ number_format($stats['quotes']) }}</div>
            <div class="stat-label">{{ app()->getLocale() === 'fr' ? 'Devis' : 'Quotes' }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">💬</div>
            <div class="stat-value">{{ number_format($stats['conversations']) }}</div>
            <div class="stat-label">{{ app()->getLocale() === 'fr' ? 'Conversations' : 'Conversations' }}</div>
        </div>
    </div>

    <div class="grid-2">
        <div class="card">
            <h2>🧾 {{ app()->getLocale() === 'fr' ? 'Factures récentes' : 'Recent Invoices' }} <span class="badge">{{ $recentInvoices->count() }}</span></h2>
            @if($recentInvoices->isEmpty())
                <div class="empty">{{ app()->getLocale() === 'fr' ? 'Aucune facture pour le moment' : 'No invoices yet' }}</div>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ app()->getLocale() === 'fr' ? 'Client' : 'Client' }}</th>
                            <th>{{ app()->getLocale() === 'fr' ? 'Montant' : 'Amount' }}</th>
                            <th>{{ app()->getLocale() === 'fr' ? 'Statut' : 'Status' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentInvoices as $inv)
                        <tr>
                            <td>{{ $inv->number }}</td>
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
            <h2>📄 {{ app()->getLocale() === 'fr' ? 'Devis récents' : 'Recent Quotes' }} <span class="badge">{{ $recentQuotes->count() }}</span></h2>
            @if($recentQuotes->isEmpty())
                <div class="empty">{{ app()->getLocale() === 'fr' ? 'Aucun devis pour le moment' : 'No quotes yet' }}</div>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ app()->getLocale() === 'fr' ? 'Client' : 'Client' }}</th>
                            <th>{{ app()->getLocale() === 'fr' ? 'Montant' : 'Amount' }}</th>
                            <th>{{ app()->getLocale() === 'fr' ? 'Statut' : 'Status' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentQuotes as $q)
                        <tr>
                            <td>{{ $q->number }}</td>
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
</div>

</body>
</html>
