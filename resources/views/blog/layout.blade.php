<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>@yield('meta_title', __('app.blog.meta_title')) — WhatsAppBizAI</title>
    <meta name="description" content="@yield('meta_description', __('app.blog.meta_description'))">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ request()->url() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ $site->favicon_path ? asset('storage/' . $site->favicon_path) : asset('favicon.ico') }}">
    <style>
        :root { --sky: #0ea5e9; --sky-dark: #0284c7; --dark: #0f172a; --mid: #1e293b; --gray: #64748b; --light: #f8fafc; --green: #22c55e; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #fff; color: var(--dark); }

        nav { position: fixed; top: 0; width: 100%; background: rgba(255,255,255,.95); backdrop-filter: blur(10px); border-bottom: 1px solid #e2e8f0; z-index: 100; padding: 0 24px; }
        .nav-inner { max-width: 1100px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; height: 64px; }
        .logo { font-size: 20px; font-weight: 800; color: var(--dark); text-decoration: none; }
        .logo span { color: var(--sky); }
        .nav-links { display: flex; gap: 8px; align-items: center; }
        .btn-nav { padding: 8px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; }
        .btn-outline { border: 1.5px solid var(--sky); color: var(--sky); }
        .btn-primary { background: var(--sky); color: #fff; }
        .btn-primary:hover { background: var(--sky-dark); }

        .blog-hero { padding: 120px 24px 60px; text-align: center; background: linear-gradient(180deg, #f0f9ff 0%, #fff 100%); }
        .blog-hero h1 { font-size: clamp(28px, 4vw, 44px); font-weight: 900; margin-bottom: 12px; }
        .blog-hero h1 span { color: var(--sky); }
        .blog-hero p { font-size: 17px; color: var(--gray); max-width: 520px; margin: 0 auto; line-height: 1.7; }

        .blog-container { max-width: 1100px; margin: 0 auto; padding: 40px 24px 80px; }

        .posts-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 28px; }

        .post-card { background: var(--light); border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; transition: box-shadow .2s, transform .2s; text-decoration: none; color: inherit; display: flex; flex-direction: column; }
        .post-card:hover { box-shadow: 0 12px 36px rgba(0,0,0,.08); transform: translateY(-2px); }
        .post-card-img { width: 100%; height: 200px; object-fit: cover; background: #e2e8f0; }
        .post-card-body { padding: 24px; flex: 1; display: flex; flex-direction: column; }
        .post-card-category { display: inline-block; background: #e0f2fe; color: var(--sky-dark); font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 20px; margin-bottom: 12px; width: fit-content; }
        .post-card-title { font-size: 18px; font-weight: 700; margin-bottom: 8px; line-height: 1.3; }
        .post-card-excerpt { font-size: 14px; color: var(--gray); line-height: 1.6; margin-bottom: 16px; flex: 1; }
        .post-card-meta { font-size: 13px; color: #94a3b8; display: flex; justify-content: space-between; align-items: center; }

        .article-header { padding: 120px 24px 40px; text-align: center; max-width: 800px; margin: 0 auto; }
        .article-header .category { display: inline-block; background: #e0f2fe; color: var(--sky-dark); font-size: 13px; font-weight: 700; padding: 4px 14px; border-radius: 20px; margin-bottom: 16px; }
        .article-header h1 { font-size: clamp(26px, 4vw, 40px); font-weight: 900; line-height: 1.2; margin-bottom: 16px; }
        .article-meta { font-size: 14px; color: var(--gray); display: flex; gap: 20px; justify-content: center; flex-wrap: wrap; }

        .article-content { max-width: 760px; margin: 0 auto; padding: 0 24px 80px; }
        .article-content img { max-width: 100%; border-radius: 10px; margin: 24px 0; }
        .article-content h2 { font-size: 24px; font-weight: 700; margin: 32px 0 16px; }
        .article-content h3 { font-size: 20px; font-weight: 700; margin: 24px 0 12px; }
        .article-content p { font-size: 16px; color: #334155; line-height: 1.8; margin-bottom: 16px; }
        .article-content ul, .article-content ol { margin: 16px 0; padding-left: 24px; }
        .article-content li { font-size: 16px; color: #334155; line-height: 1.8; margin-bottom: 6px; }
        .article-content blockquote { border-left: 4px solid var(--sky); padding: 16px 20px; background: var(--light); border-radius: 0 8px 8px 0; margin: 24px 0; font-style: italic; color: #475569; }
        .article-content a { color: var(--sky); text-decoration: underline; }
        .article-content pre { background: var(--dark); color: #e2e8f0; padding: 16px 20px; border-radius: 10px; overflow-x: auto; margin: 20px 0; font-size: 14px; }
        .article-content code { background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-size: 14px; }

        .featured-img { width: 100%; max-height: 450px; object-fit: cover; border-radius: 14px; margin-bottom: 32px; }

        .related-section { background: var(--light); padding: 60px 24px; }
        .related-inner { max-width: 1100px; margin: 0 auto; }
        .related-inner h2 { font-size: 24px; font-weight: 800; margin-bottom: 28px; text-align: center; }

        .back-link { display: inline-flex; align-items: center; gap: 6px; color: var(--sky); font-weight: 600; font-size: 14px; text-decoration: none; margin-bottom: 24px; }
        .back-link:hover { text-decoration: underline; }

        .pagination { display: flex; justify-content: center; gap: 8px; margin-top: 48px; }
        .pagination a, .pagination span { padding: 8px 14px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; }
        .pagination a { border: 1.5px solid #e2e8f0; color: var(--dark); }
        .pagination a:hover { border-color: var(--sky); color: var(--sky); }
        .pagination .active { background: var(--sky); color: #fff; border: 1.5px solid var(--sky); }

        footer { background: var(--dark); padding: 48px 24px 24px; }
        .footer-inner { max-width: 1100px; margin: 0 auto; display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 40px; }
        .footer-brand .logo { font-size: 22px; font-weight: 800; color: #fff; text-decoration: none; display: block; margin-bottom: 12px; }
        .footer-brand .logo span { color: var(--sky); }
        .footer-brand p { font-size: 14px; color: #94a3b8; line-height: 1.6; max-width: 280px; }
        .footer-col h4 { font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 16px; }
        .footer-col a { display: block; font-size: 14px; color: #94a3b8; text-decoration: none; margin-bottom: 8px; }
        .footer-col a:hover { color: var(--sky); }
        .footer-bottom { max-width: 1100px; margin: 32px auto 0; padding-top: 24px; border-top: 1px solid #1e293b; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
        .footer-bottom p { font-size: 13px; color: #475569; }
        .footer-bottom a { color: #94a3b8; text-decoration: none; }
        .footer-bottom a:hover { color: var(--sky); }

        @media (max-width: 768px) {
            .posts-grid { grid-template-columns: 1fr; }
            .footer-inner { grid-template-columns: 1fr 1fr; }
            .footer-bottom { flex-direction: column; text-align: center; }
        }
    </style>
</head>
<body>

<nav>
    <div class="nav-inner">
        @if($site->logo_path)
            <a href="{{ url('/') }}"><img src="{{ asset('storage/' . $site->logo_path) }}" alt="{{ $site->site_name ?? 'WhatsAppBizAI' }}" style="height:36px;"></a>
        @else
            @php $siteName = $site->site_name ?? 'WhatsAppBizAI'; $parts = explode('BizAI', $siteName); @endphp
            <a href="{{ url('/') }}" class="logo">{!! $parts[0] ?? $siteName !!}<span>{{ str_contains($siteName, 'BizAI') ? 'BizAI' : '' }}</span></a>
        @endif
        <div class="nav-links">
            <a href="{{ url('/') }}" class="btn-nav btn-outline" data-t-key="nav.home">Accueil</a>
            <a href="{{ url('blog') }}" class="btn-nav btn-outline" style="background:var(--sky);color:#fff;border-color:var(--sky);">Blog</a>
            <a href="{{ url('pricing') }}" class="btn-nav btn-outline" data-t-key="nav.pricing">Tarifs</a>
            <a href="{{ url('login') }}" class="btn-nav btn-primary" data-t-key="nav.login">Connexion</a>
        </div>
    </div>
</nav>

@yield('content')

<footer>
    <div class="footer-inner">
        <div class="footer-brand">
            @php $sName = $site->site_name ?? 'WhatsAppBizAI'; $p = explode('BizAI', $sName); @endphp
            <a href="{{ url('/') }}" class="logo">{!! $p[0] ?? $sName !!}<span>{{ str_contains($sName, 'BizAI') ? 'BizAI' : '' }}</span></a>
            <p>{!! $site->footer_description ?? __('app.landing.footer_desc') !!}</p>
        </div>
        <div class="footer-col">
            <h4>Produit</h4>
            <a href="{{ url('pricing') }}" data-t-key="nav.pricing">Tarifs</a>
            <a href="{{ url('register') }}" data-t-key="nav.register">S'inscrire</a>
            <a href="{{ url('login') }}" data-t-key="nav.login">Connexion</a>
        </div>
        <div class="footer-col">
            <h4>Contenu</h4>
            <a href="{{ url('blog') }}">Blog</a>
        </div>
        <div class="footer-col">
            <h4 data-t-key="landing.footer_legal">Légal</h4>
            <a href="{{ url('privacy') }}" data-t-key="nav.privacy">Confidentialité</a>
            <a href="{{ url('terms') }}" data-t-key="nav.terms">Conditions</a>
        </div>
    </div>
    <div class="footer-bottom">
        <p>{!! $site->footer_copyright ?? '© ' . date('Y') . ' WhatsAppBizAI. Tous droits réservés.' !!}</p>
        <div>
            <a href="{{ url('privacy') }}" data-t-key="nav.privacy">Confidentialité</a> ·
            <a href="{{ url('terms') }}" data-t-key="nav.terms">Conditions</a> ·
            <a href="{{ url('blog') }}">Blog</a>
        </div>
    </div>
</footer>

</body>
</html>
