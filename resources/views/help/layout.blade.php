<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>@yield('meta_title', __('app.help.meta_title')) — {{ $site->trans('site_name') ?? 'WhatsAppBizAI' }}</title>
    <meta name="description" content="@yield('meta_description', __('app.help.meta_description'))">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ request()->url() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:title" content="@yield('meta_title', __('app.help.meta_title'))">
    <meta property="og:description" content="@yield('meta_description', __('app.help.meta_description'))">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ request()->url() }}">
    <link rel="icon" type="image/x-icon" href="{{ $site->favicon_path ? asset('storage/'.$site->favicon_path) : asset('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/switchers.css') }}">
    @yield('schema')
    <style>
        :root{--sky:#0ea5e9;--sky-dark:#0284c7;--dark:#0f172a;--mid:#1e293b;--gray:#64748b;--light:#f8fafc;--green:#22c55e;}
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:#fff;color:var(--dark);}

        /* NAV */
        nav{position:fixed;top:0;width:100%;background:rgba(255,255,255,.95);backdrop-filter:blur(10px);border-bottom:1px solid #e2e8f0;z-index:100;padding:0 24px;}
        .nav-inner{max-width:1200px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;height:64px;}
        .logo{font-size:20px;font-weight:800;color:var(--dark);text-decoration:none;}
        .logo span{color:var(--sky);}
        .nav-links{display:flex;gap:8px;align-items:center;}
        .btn-nav{padding:8px 18px;border-radius:8px;font-size:14px;font-weight:600;text-decoration:none;}
        .btn-outline{border:1.5px solid var(--sky);color:var(--sky);}
        .btn-primary{background:var(--sky);color:#fff;}
        .btn-primary:hover{background:var(--sky-dark);}

        /* HERO */
        .help-hero{padding:110px 24px 56px;text-align:center;background:linear-gradient(160deg,#f0f9ff 0%,#e0f2fe 50%,#fff 100%);}
        .help-hero .badge{display:inline-block;background:rgba(14,165,233,.12);color:var(--sky-dark);font-size:13px;font-weight:700;padding:4px 14px;border-radius:20px;margin-bottom:14px;}
        .help-hero h1{font-size:clamp(28px,5vw,52px);font-weight:900;line-height:1.15;margin-bottom:14px;}
        .help-hero h1 span{color:var(--sky);}
        .help-hero p{font-size:17px;color:var(--gray);max-width:540px;margin:0 auto 28px;line-height:1.7;}

        /* SEARCH */
        .search-bar{max-width:600px;margin:0 auto;position:relative;}
        .search-bar input{width:100%;padding:16px 56px 16px 20px;border-radius:14px;border:2px solid #e2e8f0;font-size:16px;background:#fff;transition:border .2s;outline:none;}
        .search-bar input:focus{border-color:var(--sky);}
        .search-bar button{position:absolute;right:12px;top:50%;transform:translateY(-50%);background:var(--sky);border:none;color:#fff;width:36px;height:36px;border-radius:10px;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:16px;}

        /* LAYOUT */
        .help-layout{max-width:1200px;margin:0 auto;padding:48px 24px 80px;display:grid;grid-template-columns:260px 1fr;gap:40px;}
        .help-layout.no-hero{padding-top:100px;}
        .help-sidebar{position:sticky;top:88px;height:fit-content;}
        .sidebar-title{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;margin-bottom:12px;}
        .sidebar-cat{display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:10px;text-decoration:none;color:var(--dark);font-size:14px;font-weight:500;margin-bottom:4px;transition:background .15s;}
        .sidebar-cat:hover,.sidebar-cat.active{background:var(--light);color:var(--sky);}
        .sidebar-cat .cat-icon{font-size:18px;width:28px;text-align:center;}
        .sidebar-cat .cat-count{margin-left:auto;font-size:12px;color:#94a3b8;background:#f1f5f9;padding:2px 8px;border-radius:10px;}

        /* CONTENT */
        .help-main .breadcrumb{font-size:13px;color:var(--gray);margin-bottom:20px;padding-top:4px;}
        .help-main .breadcrumb a{color:var(--sky);text-decoration:none;}
        .help-main .breadcrumb a:hover{text-decoration:underline;}

        /* CATEGORY GRID */
        .cats-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;margin-bottom:48px;}
        .cat-card{background:var(--light);border:1px solid #e2e8f0;border-radius:16px;padding:28px 24px;text-decoration:none;color:inherit;transition:box-shadow .2s,transform .2s;display:flex;flex-direction:column;gap:12px;}
        .cat-card:hover{box-shadow:0 12px 36px rgba(0,0,0,.08);transform:translateY(-2px);}
        .cat-card-icon{font-size:36px;width:60px;height:60px;border-radius:14px;display:flex;align-items:center;justify-content:center;}
        .cat-card h3{font-size:18px;font-weight:700;}
        .cat-card p{font-size:14px;color:var(--gray);line-height:1.6;flex:1;}
        .cat-card-footer{display:flex;justify-content:space-between;align-items:center;font-size:13px;color:#94a3b8;}
        .cat-card-footer span{color:var(--sky);font-weight:600;}

        /* ARTICLE CARD */
        .articles-list{display:flex;flex-direction:column;gap:16px;}
        .art-card{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:20px 24px;text-decoration:none;color:inherit;display:flex;gap:20px;align-items:flex-start;transition:box-shadow .15s,border-color .15s;}
        .art-card:hover{box-shadow:0 8px 24px rgba(0,0,0,.07);border-color:var(--sky);}
        .art-card-icon{font-size:24px;flex-shrink:0;width:44px;height:44px;background:var(--light);border-radius:12px;display:flex;align-items:center;justify-content:center;}
        .art-card-body{flex:1;}
        .art-card-body h3{font-size:16px;font-weight:700;margin-bottom:6px;line-height:1.4;}
        .art-card-body p{font-size:14px;color:var(--gray);line-height:1.5;}
        .art-card-meta{display:flex;gap:10px;align-items:center;margin-top:10px;flex-wrap:wrap;}
        .badge-type{display:inline-block;font-size:11px;font-weight:700;padding:2px 9px;border-radius:10px;}
        .badge-article{background:#dbeafe;color:#1d4ed8;}
        .badge-tutorial{background:#dcfce7;color:#15803d;}
        .badge-guide{background:#fef9c3;color:#854d0e;}
        .badge-difficulty{display:inline-block;font-size:11px;font-weight:700;padding:2px 9px;border-radius:10px;background:#f1f5f9;color:var(--gray);}
        .art-card-arrow{flex-shrink:0;color:#94a3b8;font-size:20px;margin-top:2px;}

        /* ARTICLE CONTENT */
        .article-wrap{max-width:820px;}
        .article-header{margin-bottom:32px;}
        .article-header .breadcrumb{font-size:13px;color:var(--gray);margin-bottom:14px;}
        .article-header .breadcrumb a{color:var(--sky);text-decoration:none;}
        .article-header .breadcrumb a:hover{text-decoration:underline;}
        .article-header h1{font-size:clamp(24px,3.5vw,38px);font-weight:900;line-height:1.2;margin-bottom:14px;}
        .article-meta-row{display:flex;gap:14px;flex-wrap:wrap;align-items:center;margin-bottom:24px;font-size:13px;color:var(--gray);}
        .article-meta-row .sep{color:#cbd5e1;}
        .featured-img{width:100%;max-height:420px;object-fit:cover;border-radius:14px;margin-bottom:32px;}
        .article-body h2{font-size:22px;font-weight:700;margin:32px 0 14px;padding-top:8px;border-top:1px solid #f1f5f9;}
        .article-body h3{font-size:18px;font-weight:700;margin:24px 0 10px;}
        .article-body p{font-size:16px;color:#334155;line-height:1.8;margin-bottom:16px;}
        .article-body ul,.article-body ol{margin:16px 0 16px 24px;}
        .article-body li{font-size:16px;color:#334155;line-height:1.8;margin-bottom:6px;}
        .article-body blockquote{border-left:4px solid var(--sky);padding:14px 20px;background:var(--light);border-radius:0 10px 10px 0;margin:24px 0;font-style:italic;color:#475569;}
        .article-body a{color:var(--sky);text-decoration:underline;}
        .article-body pre{background:var(--dark);color:#e2e8f0;padding:16px 20px;border-radius:10px;overflow-x:auto;margin:20px 0;font-size:14px;}
        .article-body code{background:#f1f5f9;padding:2px 6px;border-radius:4px;font-size:14px;}
        .article-body table{width:100%;border-collapse:collapse;margin:20px 0;}
        .article-body th{background:var(--light);padding:10px 14px;font-weight:700;font-size:14px;text-align:left;border:1px solid #e2e8f0;}
        .article-body td{padding:10px 14px;font-size:14px;border:1px solid #e2e8f0;}

        /* GUIDE STEPS */
        .guide-steps{margin:32px 0;}
        .guide-steps h2{font-size:22px;font-weight:700;margin-bottom:20px;}
        .step-item{display:flex;gap:20px;margin-bottom:24px;padding:20px 24px;background:var(--light);border-radius:14px;border-left:4px solid var(--sky);}
        .step-num{flex-shrink:0;width:36px;height:36px;background:var(--sky);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:15px;}
        .step-content h3{font-size:16px;font-weight:700;margin-bottom:6px;}
        .step-content p{font-size:14px;color:var(--gray);line-height:1.6;}

        /* RELATED */
        .related-section{background:var(--light);border-radius:16px;padding:28px;margin-top:48px;}
        .related-section h2{font-size:18px;font-weight:700;margin-bottom:16px;}
        .related-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;}
        .related-card{background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:14px;text-decoration:none;color:inherit;font-size:14px;font-weight:600;transition:border-color .15s;}
        .related-card:hover{border-color:var(--sky);color:var(--sky);}

        /* SEARCH RESULTS */
        .search-hero{padding:110px 24px 40px;text-align:center;background:var(--light);}
        .search-hero h1{font-size:28px;font-weight:800;margin-bottom:8px;}
        .search-results-count{font-size:15px;color:var(--gray);}

        /* FOOTER */
        footer{background:var(--dark);padding:48px 24px 24px;}
        .footer-inner{max-width:1200px;margin:0 auto;display:grid;grid-template-columns:2fr 1fr 1fr 1fr 1fr;gap:32px;}
        .footer-brand .logo{font-size:22px;font-weight:800;color:#fff;text-decoration:none;display:block;margin-bottom:12px;}
        .footer-brand .logo span{color:var(--sky);}
        .footer-brand p{font-size:14px;color:#94a3b8;line-height:1.6;max-width:260px;}
        .footer-col h4{font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#fff;margin-bottom:14px;}
        .footer-col a{display:block;font-size:14px;color:#94a3b8;text-decoration:none;margin-bottom:8px;}
        .footer-col a:hover{color:var(--sky);}
        .footer-bottom{max-width:1200px;margin:28px auto 0;padding-top:20px;border-top:1px solid #1e293b;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;}
        .footer-bottom p{font-size:13px;color:#475569;}
        .footer-bottom a{color:#94a3b8;text-decoration:none;font-size:13px;}
        .footer-bottom a:hover{color:var(--sky);}

        /* RESPONSIVE */
        @media(max-width:900px){
            .help-layout{grid-template-columns:1fr;}
            .help-sidebar{display:none;}
            .footer-inner{grid-template-columns:1fr 1fr;}
        }
        @media(max-width:600px){
            .cats-grid{grid-template-columns:1fr;}
            .art-card{flex-direction:column;}
            .footer-inner{grid-template-columns:1fr;}
        }
    </style>
</head>
<body>
<nav>
    <div class="nav-inner">
        @if($site->logo_path)
            <a href="{{ url('/') }}"><img src="{{ asset('storage/'.$site->logo_path) }}" alt="{{ $site->trans('site_name') ?? 'WhatsAppBizAI' }}" style="height:36px;"></a>
        @else
            @php $siteName=$site->trans('site_name')??'WhatsAppBizAI';$parts=explode('BizAI',$siteName); @endphp
            <a href="{{ url('/') }}" class="logo">{!! $parts[0]??$siteName !!}<span>{{ str_contains($siteName,'BizAI')?'BizAI':'' }}</span></a>
        @endif
        <div class="nav-links">
            <div class="switcher-wrap light-theme">
                <button class="switcher-btn lang-btn" data-lang="fr">FR</button>
                <button class="switcher-btn lang-btn" data-lang="en">EN</button>
            </div>
            <a href="{{ url('/') }}" class="btn-nav btn-outline">{{ app()->getLocale()==='en'?'Home':'Accueil' }}</a>
            <a href="{{ url('help') }}" class="btn-nav btn-outline" style="background:var(--sky);color:#fff;border-color:var(--sky);">Help Center</a>
            <a href="{{ url('login') }}" class="btn-nav btn-primary">{{ app()->getLocale()==='en'?'Login':'Connexion' }}</a>
        </div>
    </div>
</nav>

@yield('content')

<footer>
    <div class="footer-inner">
        <div class="footer-brand">
            @php $sN=$site->trans('site_name')??'WhatsAppBizAI';$p=explode('BizAI',$sN); @endphp
            <a href="{{ url('/') }}" class="logo">{!! $p[0]??$sN !!}<span>{{ str_contains($sN,'BizAI')?'BizAI':'' }}</span></a>
            <p>{!! $site->trans('footer_description')??__('app.landing.footer_desc') !!}</p>
        </div>
        <div class="footer-col">
            <h4>{{ app()->getLocale()==='en'?'Product':'Produit' }}</h4>
            <a href="{{ url('pricing') }}">{{ app()->getLocale()==='en'?'Pricing':'Tarifs' }}</a>
            <a href="{{ url('register') }}">{{ app()->getLocale()==='en'?'Sign up':'S\'inscrire' }}</a>
            <a href="{{ url('login') }}">{{ app()->getLocale()==='en'?'Login':'Connexion' }}</a>
        </div>
        <div class="footer-col">
            <h4>{{ app()->getLocale()==='en'?'Resources':'Ressources' }}</h4>
            <a href="{{ url('blog') }}">Blog</a>
            <a href="{{ url('help') }}">Help Center</a>
            <a href="{{ url('contact') }}">{{ app()->getLocale()==='en'?'Contact':'Contact' }}</a>
        </div>
        <div class="footer-col">
            <h4>Help Center</h4>
            @foreach(\App\Models\HelpCategory::active()->limit(5)->get() as $hc)
                <a href="{{ url('help/'.$hc->slug) }}">{{ $hc->trans('name') }}</a>
            @endforeach
        </div>
        <div class="footer-col">
            <h4>{{ app()->getLocale()==='en'?'Legal':'Légal' }}</h4>
            <a href="{{ url('privacy') }}">{{ app()->getLocale()==='en'?'Privacy':'Confidentialité' }}</a>
            <a href="{{ url('terms') }}">{{ app()->getLocale()==='en'?'Terms':'Conditions' }}</a>
        </div>
    </div>
    <div class="footer-bottom">
        <p>{!! $site->trans('footer_copyright')?? '© '.date('Y').' WhatsAppBizAI. '.( app()->getLocale()==='en'?'All rights reserved.':'Tous droits réservés.') !!}</p>
        <div>
            <a href="{{ url('privacy') }}">{{ app()->getLocale()==='en'?'Privacy':'Confidentialité' }}</a> ·
            <a href="{{ url('terms') }}">{{ app()->getLocale()==='en'?'Terms':'Conditions' }}</a> ·
            <a href="{{ url('blog') }}">Blog</a> ·
            <a href="{{ url('help') }}">Help Center</a>
        </div>
    </div>
</footer>

<script>
window.__langSwitchUrl='{{ url("language") }}';
window.__i18n={
    fr:{!! json_encode(['nav'=>trans('app.nav',[],'fr'),'landing'=>trans('app.landing',[],'fr')]) !!},
    en:{!! json_encode(['nav'=>trans('app.nav',[],'en'),'landing'=>trans('app.landing',[],'en')]) !!}
};
</script>
<script src="{{ asset('js/preferences.js') }}?v={{ time() }}"></script>
</body>
</html>
