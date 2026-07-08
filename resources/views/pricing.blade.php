@section('meta_title', 'Tarifs ' . ($site->trans('site_name') ?? 'WhatsAppBizAI') . ' — Plans Gratuit, Starter, Business')
@section('meta_description', 'Découvrez les tarifs ' . ($site->trans('site_name') ?? 'WhatsAppBizAI') . '. Plan gratuit pour démarrer, Starter et Business pour les PME en croissance.')
@section('canonical_url', url('/pricing'))
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    @include('components.seo')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ $site->favicon_path ? asset('storage/' . $site->favicon_path) : asset('favicon.ico') }}">

    <link rel="stylesheet" href="{{ asset('css/switchers.css') }}">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:#0f172a;color:#fff;min-height:100vh}
        nav{display:flex;justify-content:space-between;align-items:center;padding:20px 40px;border-bottom:1px solid #1e293b}
        .logo{font-size:20px;font-weight:800;color:#0ea5e9;text-decoration:none}
        nav a{color:#94a3b8;text-decoration:none;margin-left:20px;font-size:14px}
        nav a:hover{color:#fff}
        .nav-right{display:flex;align-items:center;gap:16px}
        .hero{text-align:center;padding:60px 20px 40px}
        .hero h1{font-size:42px;font-weight:900;margin-bottom:12px}
        .hero p{color:#94a3b8;font-size:18px}
        .toggle{display:flex;justify-content:center;gap:0;margin:32px auto;background:#1e293b;border-radius:50px;width:fit-content;padding:4px}
        .toggle button{padding:10px 28px;border:none;border-radius:50px;cursor:pointer;font-size:14px;font-weight:600;transition:all .2s}
        .toggle button.active{background:#0ea5e9;color:#fff}
        .toggle button:not(.active){background:transparent;color:#94a3b8}
        .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:24px;max-width:1100px;margin:0 auto;padding:0 24px 80px}
        .card{background:#1e293b;border-radius:16px;padding:32px;border:2px solid transparent;transition:border .2s}
        .card.popular{border-color:#0ea5e9;position:relative}
        .badge{position:absolute;top:-12px;left:50%;transform:translateX(-50%);background:#0ea5e9;color:#fff;font-size:11px;font-weight:700;padding:4px 14px;border-radius:20px;white-space:nowrap}
        .plan-name{font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#0ea5e9;margin-bottom:8px}
        .price{font-size:36px;font-weight:900;margin-bottom:4px}
        .price span{font-size:14px;font-weight:400;color:#94a3b8}
        .desc{color:#94a3b8;font-size:14px;margin-bottom:24px}
        .features{list-style:none;margin-bottom:28px}
        .features li{padding:8px 0;border-bottom:1px solid #334155;font-size:14px;display:flex;align-items:center;gap:8px}
        .features li::before{content:'✓';color:#22c55e;font-weight:700;flex-shrink:0}
        .features li.no::before{content:'✗';color:#ef4444}
        .btn{display:block;text-align:center;padding:14px;border-radius:10px;font-weight:700;font-size:15px;cursor:pointer;border:none;width:100%;text-decoration:none;transition:opacity .2s}
        .btn:hover{opacity:.85}
        .btn-primary{background:#0ea5e9;color:#fff}
        .btn-outline{background:transparent;border:2px solid #334155;color:#fff}
        .btn-free{background:#22c55e;color:#fff}
        .manual-link{text-align:center;margin-top:12px;font-size:13px;color:#64748b}
        .manual-link a{color:#0ea5e9;text-decoration:none}
        footer{text-align:center;padding:32px;color:#475569;font-size:13px;border-top:1px solid #1e293b}

        @media(max-width:768px){.hero h1{font-size:28px}.grid{grid-template-columns:1fr}nav{flex-wrap:wrap;gap:12px}}
    </style>
</head>
<body>

<nav>
    <a class="logo" href="{{ url('/') }}">🟢 {{ $site->trans('site_name') ?? 'WhatsAppBizAI' }}</a>
    <div class="nav-right">
        <div class="switcher-wrap">
            <button class="switcher-btn lang-btn" data-lang="fr">FR</button>
            <button class="switcher-btn lang-btn" data-lang="en">EN</button>
            <div class="switcher-sep"></div>
            <button class="switcher-btn currency-btn" data-currency="USD">USD</button>
            <button class="switcher-btn currency-btn" data-currency="XAF">XAF</button>
            <button class="switcher-btn currency-btn" data-currency="XOF">XOF</button>
            <button class="switcher-btn currency-btn" data-currency="EUR">EUR</button>
            <button class="switcher-btn currency-btn" data-currency="GBP">GBP</button>
            <button class="switcher-btn currency-btn" data-currency="NGN">NGN</button>
            <button class="switcher-btn currency-btn" data-currency="GHS">GHS</button>
            <button class="switcher-btn currency-btn" data-currency="KES">KES</button>
            <button class="switcher-btn currency-btn" data-currency="ZAR">ZAR</button>
            <button class="switcher-btn currency-btn" data-currency="MAD">MAD</button>
        </div>
        <a href="{{ url('/') }}" data-t-key="nav.home">Accueil</a>
        <a href="{{ url('register') }}" data-t-key="nav.register">Démarrer</a>
        <a href="/admin" data-t-key="nav.dashboard">Dashboard</a>
    </div>
</nav>

<div class="hero">
    <h1 data-t-key="pricing.hero_title">Des tarifs simples et transparents</h1>
    <p data-t-key="pricing.hero_desc">Commencez gratuitement. Évoluez quand vous êtes prêt.</p>
</div>

<div class="toggle">
    <button class="active" id="btn-monthly" onclick="switchCycle('monthly')" data-t-key="pricing.monthly">Mensuel</button>
    <button id="btn-yearly" onclick="switchCycle('yearly')"><span data-t-key="pricing.yearly">Annuel</span> <span style="color:#22c55e;font-size:11px">-17%</span></button>
</div>

<div class="grid">

    {{-- FREE --}}
    <div class="card">
        <div class="plan-name" data-t-key="pricing.plan_free">Gratuit</div>
        <div class="price monthly-price" data-xaf="0" data-period="monthly">0 <span>XAF/mois</span></div>
        <div class="price yearly-price" style="display:none" data-xaf="0" data-period="yearly">0 <span>XAF/an</span></div>
        <div class="desc" data-t-key="pricing.desc_free">Pour démarrer et tester l'outil.</div>
        <ul class="features">
            <li data-t-key="pricing.f_free_1">50 contacts</li>
            <li data-t-key="pricing.f_free_2">10 factures/mois</li>
            <li data-t-key="pricing.f_free_3">100 messages IA</li>
            <li class="no" data-t-key="pricing.f_free_4">Envoi PDF WhatsApp</li>
            <li class="no" data-t-key="pricing.f_free_5">Relances automatiques</li>
            <li class="no" data-t-key="pricing.f_free_6">Marketing de masse</li>
        </ul>
        <a href="{{ url('register') }}" class="btn btn-free" data-t-key="pricing.get_started">Commencer gratuitement</a>
    </div>

    {{-- STARTER --}}
    <div class="card">
        <div class="plan-name" data-t-key="pricing.plan_starter">Starter</div>
        <div class="price monthly-price" data-xaf="9900" data-period="monthly">9 900 <span>XAF/mois</span></div>
        <div class="price yearly-price" style="display:none" data-xaf="99000" data-period="yearly">99 000 <span>XAF/an</span></div>
        <div class="desc" data-t-key="pricing.desc_starter">Pour les indépendants et micro-entreprises.</div>
        <ul class="features">
            <li data-t-key="pricing.f_starter_1">500 contacts</li>
            <li data-t-key="pricing.f_starter_2">100 factures/mois</li>
            <li data-t-key="pricing.f_starter_3">1 000 messages IA</li>
            <li data-t-key="pricing.f_starter_4">Envoi PDF WhatsApp</li>
            <li data-t-key="pricing.f_starter_5">Relances automatiques</li>
            <li class="no" data-t-key="pricing.f_starter_6">Marketing de masse</li>
        </ul>
        @auth
        <form method="POST" action="{{ route('payment.initiate') }}">
            @csrf
            <input type="hidden" name="plan" value="starter">
            <input type="hidden" class="cycle-input" name="cycle" value="monthly">
            <input type="hidden" name="currency" value="{{ auth()->user()->business->currency ?? 'XAF' }}">
            <button type="submit" class="btn btn-primary" data-t-key="pricing.subscribe">Souscrire</button>
        </form>
        @else
        <a href="{{ url('register') }}" class="btn btn-primary" data-t-key="pricing.get_started">Commencer</a>
        @endauth
        <div class="manual-link"><a href="{{ route('payment.manual.form') }}?plan=starter" data-t-key="pricing.pay_manual">Payer manuellement (MoMo/Orange)</a></div>
    </div>

    {{-- BUSINESS --}}
    <div class="card popular">
        <div class="badge" data-t-key="pricing.popular">⭐ Le plus populaire</div>
        <div class="plan-name" data-t-key="pricing.plan_business">Business</div>
        <div class="price monthly-price" data-xaf="24900" data-period="monthly">24 900 <span>XAF/mois</span></div>
        <div class="price yearly-price" style="display:none" data-xaf="249000" data-period="yearly">249 000 <span>XAF/an</span></div>
        <div class="desc" data-t-key="pricing.desc_business">Pour les PME en croissance.</div>
        <ul class="features">
            <li data-t-key="pricing.f_biz_1">2 000 contacts</li>
            <li data-t-key="pricing.f_biz_2">500 factures/mois</li>
            <li data-t-key="pricing.f_biz_3">5 000 messages IA</li>
            <li data-t-key="pricing.f_biz_4">Envoi PDF WhatsApp</li>
            <li data-t-key="pricing.f_biz_5">Relances automatiques</li>
            <li data-t-key="pricing.f_biz_6">Marketing de masse</li>
        </ul>
        @auth
        <form method="POST" action="{{ route('payment.initiate') }}">
            @csrf
            <input type="hidden" name="plan" value="business">
            <input type="hidden" class="cycle-input" name="cycle" value="monthly">
            <input type="hidden" name="currency" value="{{ auth()->user()->business->currency ?? 'XAF' }}">
            <button type="submit" class="btn btn-primary" data-t-key="pricing.subscribe">Souscrire</button>
        </form>
        @else
        <a href="{{ url('register') }}" class="btn btn-primary" data-t-key="pricing.get_started">Commencer</a>
        @endauth
        <div class="manual-link"><a href="{{ route('payment.manual.form') }}?plan=business" data-t-key="pricing.pay_manual">Payer manuellement</a></div>
    </div>

    {{-- PRO --}}
    <div class="card">
        <div class="plan-name" data-t-key="pricing.plan_pro">Pro</div>
        <div class="price monthly-price" data-xaf="49900" data-period="monthly">49 900 <span>XAF/mois</span></div>
        <div class="price yearly-price" style="display:none" data-xaf="499000" data-period="yearly">499 000 <span>XAF/an</span></div>
        <div class="desc" data-t-key="pricing.desc_pro">Pour les équipes et grandes PME.</div>
        <ul class="features">
            <li data-t-key="pricing.f_pro_1">Contacts illimités</li>
            <li data-t-key="pricing.f_pro_2">Factures illimitées</li>
            <li data-t-key="pricing.f_pro_3">Messages IA illimités</li>
            <li data-t-key="pricing.f_pro_4">Tout Business +</li>
            <li data-t-key="pricing.f_pro_5">Support prioritaire</li>
            <li data-t-key="pricing.f_pro_6">Intégrations avancées</li>
        </ul>
        @auth
        <form method="POST" action="{{ route('payment.initiate') }}">
            @csrf
            <input type="hidden" name="plan" value="pro">
            <input type="hidden" class="cycle-input" name="cycle" value="monthly">
            <input type="hidden" name="currency" value="{{ auth()->user()->business->currency ?? 'XAF' }}">
            <button type="submit" class="btn btn-primary" data-t-key="pricing.subscribe">Souscrire</button>
        </form>
        @else
        <a href="{{ url('register') }}" class="btn btn-primary" data-t-key="pricing.get_started">Commencer</a>
        @endauth
        <div class="manual-link"><a href="{{ route('payment.manual.form') }}?plan=pro" data-t-key="pricing.pay_manual">Payer manuellement</a></div>
    </div>

</div>

<footer>
    <p>{!! $site->footer_copyright ?? '© ' . date('Y') . ' WhatsAppBizAI' !!} · <a href="{{ url('/') }}" style="color:#0ea5e9" data-t-key="nav.home">Accueil</a> · <a href="/admin" style="color:#0ea5e9" data-t-key="nav.dashboard">Dashboard</a></p>
</footer>

<script>
window.__langSwitchUrl = '{{ url("language") }}';
window.__i18n = {
    fr: {!! json_encode([
        'nav' => trans('app.nav', [], 'fr'),
        'pricing' => trans('app.pricing', [], 'fr'),
        'per_month' => trans('app.per_month', [], 'fr'),
        'per_year' => trans('app.per_year', [], 'fr'),
    ]) !!},
    en: {!! json_encode([
        'nav' => trans('app.nav', [], 'en'),
        'pricing' => trans('app.pricing', [], 'en'),
        'per_month' => trans('app.per_month', [], 'en'),
        'per_year' => trans('app.per_year', [], 'en'),
    ]) !!}
};
</script>
<script src="{{ asset('js/preferences.js') }}?v={{ time() }}"></script>
<script>
function switchCycle(cycle) {
    document.querySelectorAll('.monthly-price').forEach(function(el) { el.style.display = cycle === 'monthly' ? '' : 'none'; });
    document.querySelectorAll('.yearly-price').forEach(function(el) { el.style.display = cycle === 'yearly' ? '' : 'none'; });
    document.querySelectorAll('.cycle-input').forEach(function(el) { el.value = cycle; });
    document.getElementById('btn-monthly').classList.toggle('active', cycle === 'monthly');
    document.getElementById('btn-yearly').classList.toggle('active', cycle === 'yearly');
}
</script>

</body>
</html>
