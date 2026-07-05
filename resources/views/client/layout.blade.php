<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — WhatsAppBizAI</title>
    <style>
        :root { --sky: #0ea5e9; --sky-dark: #0284c7; --dark: #0f172a; --mid: #1e293b; --gray: #64748b; --light: #f8fafc; --green: #22c55e; --red: #ef4444; --orange: #f59e0b; --border: #e2e8f0; --sidebar-w: 260px; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f1f5f9; color: var(--dark); min-height: 100vh; }

        .sidebar { position: fixed; top: 0; left: 0; bottom: 0; width: var(--sidebar-w); background: var(--dark); color: #fff; display: flex; flex-direction: column; z-index: 50; transition: transform .2s; }
        .sidebar-header { padding: 20px 20px 16px; border-bottom: 1px solid rgba(255,255,255,.08); }
        .sidebar-header .logo { font-size: 18px; font-weight: 800; color: #fff; text-decoration: none; display: block; }
        .sidebar-header .logo span { color: var(--sky); }
        .sidebar-header .plan { font-size: 11px; background: var(--sky); color: #fff; padding: 2px 8px; border-radius: 10px; display: inline-block; margin-top: 6px; font-weight: 600; text-transform: uppercase; }

        .sidebar-nav { flex: 1; overflow-y: auto; padding: 12px 0; }
        .nav-section { padding: 8px 20px 4px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #64748b; }
        .nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 20px; font-size: 14px; color: #94a3b8; text-decoration: none; transition: all .15s; border-left: 3px solid transparent; }
        .nav-item:hover { background: rgba(255,255,255,.05); color: #e2e8f0; }
        .nav-item.active { background: rgba(14,165,233,.1); color: var(--sky); border-left-color: var(--sky); font-weight: 600; }
        .nav-item .icon { width: 20px; text-align: center; font-size: 16px; flex-shrink: 0; }
        .nav-item .badge { margin-left: auto; background: rgba(14,165,233,.2); color: var(--sky); font-size: 11px; padding: 1px 7px; border-radius: 10px; font-weight: 600; }

        .sidebar-footer { padding: 16px 20px; border-top: 1px solid rgba(255,255,255,.08); }
        .sidebar-footer .user { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
        .sidebar-footer .avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--sky); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; flex-shrink: 0; }
        .sidebar-footer .user-name { font-size: 13px; font-weight: 600; color: #e2e8f0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sidebar-footer .user-email { font-size: 11px; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sidebar-footer .logout { display: block; font-size: 13px; color: #64748b; text-decoration: none; padding: 6px 0; }
        .sidebar-footer .logout:hover { color: var(--red); }

        .main { margin-left: var(--sidebar-w); min-height: 100vh; }
        .topbar { background: #fff; border-bottom: 1px solid var(--border); padding: 0 32px; display: flex; align-items: center; justify-content: space-between; height: 56px; position: sticky; top: 0; z-index: 40; }
        .topbar h1 { font-size: 18px; font-weight: 700; }
        .topbar-right { display: flex; align-items: center; gap: 12px; }
        .topbar-right .lang-switch { display: flex; gap: 4px; }
        .topbar-right .lang-btn { padding: 4px 10px; border-radius: 6px; border: 1px solid var(--border); background: #fff; font-size: 12px; font-weight: 600; cursor: pointer; color: var(--gray); }
        .topbar-right .lang-btn.active { background: var(--sky); color: #fff; border-color: var(--sky); }

        .content { padding: 32px; }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 32px; }
        .stat-card { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 20px; }
        .stat-card .stat-icon { font-size: 24px; margin-bottom: 6px; }
        .stat-card .stat-value { font-size: 28px; font-weight: 800; }
        .stat-card .stat-label { font-size: 13px; color: var(--gray); margin-top: 2px; }

        .card { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 24px; margin-bottom: 24px; }
        .card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
        .card-header h2 { font-size: 17px; font-weight: 700; }
        .card-header .badge { font-size: 12px; background: #f1f5f9; color: var(--gray); padding: 2px 8px; border-radius: 10px; font-weight: 500; }

        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; font-size: 12px; font-weight: 600; color: var(--gray); text-transform: uppercase; padding: 8px 12px; border-bottom: 1px solid var(--border); }
        td { font-size: 14px; padding: 10px 12px; border-bottom: 1px solid #f1f5f9; }
        tr:hover td { background: #f8fafc; }

        .status { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; }
        .status.paid, .status.accepted, .status.active { background: #dcfce7; color: #166534; }
        .status.pending, .status.sent { background: #fef3c7; color: #92400e; }
        .status.overdue, .status.declined, .status.cancelled { background: #fef2f2; color: #991b1b; }
        .status.draft { background: #f1f5f9; color: var(--gray); }

        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: all .15s; }
        .btn-primary { background: var(--sky); color: #fff; }
        .btn-primary:hover { background: var(--sky-dark); }
        .btn-outline { border: 1px solid var(--border); color: var(--dark); background: #fff; }
        .btn-outline:hover { background: #f8fafc; }
        .btn-danger { background: var(--red); color: #fff; }
        .btn-danger:hover { background: #dc2626; }
        .btn-sm { padding: 5px 10px; font-size: 12px; }
        .btn-ghost { background: transparent; color: var(--sky); padding: 4px 8px; }
        .btn-ghost:hover { background: #f0f9ff; }

        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 5px; color: #374151; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 9px 12px; border: 1px solid var(--border); border-radius: 8px; font-size: 14px; font-family: inherit; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: var(--sky); box-shadow: 0 0 0 3px rgba(14,165,233,.1); }
        .form-group textarea { resize: vertical; min-height: 80px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-help { font-size: 12px; color: var(--gray); margin-top: 4px; }

        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; }
        .alert-success { background: #dcfce7; color: #166534; }
        .alert-error { background: #fef2f2; color: #991b1b; }
        .alert-warning { background: #fef3c7; color: #92400e; }

        .empty { text-align: center; padding: 40px 20px; color: var(--gray); }
        .empty .empty-icon { font-size: 40px; margin-bottom: 12px; }
        .empty p { font-size: 14px; margin-bottom: 16px; }

        .tabs { display: flex; gap: 0; border-bottom: 2px solid var(--border); margin-bottom: 24px; }
        .tab { padding: 10px 20px; font-size: 14px; font-weight: 600; color: var(--gray); text-decoration: none; border-bottom: 2px solid transparent; margin-bottom: -2px; }
        .tab.active { color: var(--sky); border-bottom-color: var(--sky); }
        .tab:hover { color: var(--dark); }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main { margin-left: 0; }
            .content { padding: 16px; }
            .topbar { padding: 0 16px; }
            .form-row { grid-template-columns: 1fr; }
        }
    </style>
    @yield('head')
</head>
<body>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="{{ url('dashboard') }}" class="logo">WhatsApp<span>BizAI</span></a>
        @if($business ?? null)
            <span class="plan">{{ ucfirst($business->plan) }} Plan</span>
        @endif
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">Menu</div>
        <a href="{{ url('dashboard') }}" class="nav-item {{ request()->routeIs('c.dashboard') ? 'active' : '' }}">
            <span class="icon">📊</span> Tableau de bord
        </a>

        <div class="nav-section">Messagerie</div>
        <a href="{{ url('client/contacts') }}" class="nav-item {{ request()->routeIs('c.contacts*') ? 'active' : '' }}">
            <span class="icon">👥</span> Contacts
            @if(($sidebarStats['contacts'] ?? 0) > 0)
                <span class="badge">{{ $sidebarStats['contacts'] }}</span>
            @endif
        </a>
        <a href="{{ url('client/conversations') }}" class="nav-item {{ request()->routeIs('c.conversations*') ? 'active' : '' }}">
            <span class="icon">💬</span> Conversations IA
            @if(($sidebarStats['conversations'] ?? 0) > 0)
                <span class="badge">{{ $sidebarStats['conversations'] }}</span>
            @endif
        </a>
        <a href="{{ url('client/broadcast') }}" class="nav-item {{ request()->routeIs('c.broadcast') ? 'active' : '' }}">
            <span class="icon">📤</span> Broadcast
        </a>
        <a href="{{ url('client/retention') }}" class="nav-item {{ request()->routeIs('c.retention') ? 'active' : '' }}">
            <span class="icon">❤️</span> Rétention
        </a>

        <div class="nav-section">Facturation</div>
        <a href="{{ url('client/quotes') }}" class="nav-item {{ request()->routeIs('c.quotes*') ? 'active' : '' }}">
            <span class="icon">📄</span> Devis
            @if(($sidebarStats['quotes'] ?? 0) > 0)
                <span class="badge">{{ $sidebarStats['quotes'] }}</span>
            @endif
        </a>
        <a href="{{ url('client/invoices') }}" class="nav-item {{ request()->routeIs('c.invoices*') ? 'active' : '' }}">
            <span class="icon">🧾</span> Factures
            @if(($sidebarStats['invoices'] ?? 0) > 0)
                <span class="badge">{{ $sidebarStats['invoices'] }}</span>
            @endif
        </a>

        <div class="nav-section">Catalogue</div>
        <a href="{{ url('client/services') }}" class="nav-item {{ request()->routeIs('c.services*') ? 'active' : '' }}">
            <span class="icon">📦</span> Services
        </a>

        <div class="nav-section">Paramètres</div>
        <a href="{{ url('client/settings/business') }}" class="nav-item {{ request()->routeIs('c.settings.business') ? 'active' : '' }}">
            <span class="icon">🏢</span> Mon entreprise
        </a>
        <a href="{{ url('client/settings/whatsapp') }}" class="nav-item {{ request()->routeIs('c.settings.whatsapp') ? 'active' : '' }}">
            <span class="icon">📱</span> WhatsApp & IA
        </a>
        <a href="{{ url('client/settings/profile') }}" class="nav-item {{ request()->routeIs('c.settings.profile') ? 'active' : '' }}">
            <span class="icon">👤</span> Mon profil
        </a>
        <a href="{{ url('client/settings/billing') }}" class="nav-item {{ request()->routeIs('c.settings.billing') ? 'active' : '' }}">
            <span class="icon">💳</span> Plan & Facturation
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user">
            <div class="avatar">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</div>
            <div>
                <div class="user-name">{{ $user->name ?? '' }}</div>
                <div class="user-email">{{ $user->email ?? '' }}</div>
            </div>
        </div>
        <a href="{{ url('logout') }}" class="logout">🚪 Déconnexion</a>
    </div>
</aside>

<main class="main">
    <div class="topbar">
        <div style="display:flex;align-items:center;gap:12px;">
            <button onclick="document.getElementById('sidebar').classList.toggle('open')" style="display:none;background:none;border:none;font-size:20px;cursor:pointer;" id="menuToggle">☰</button>
            <h1>@yield('title', 'Dashboard')</h1>
        </div>
        <div class="topbar-right">
            @yield('topbar-right')
        </div>
    </div>

    <div class="content">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>
</main>

<script>
if(window.innerWidth <= 768) {
    document.getElementById('menuToggle').style.display = 'block';
}
</script>
@yield('scripts')
</body>
</html>
