@section('meta_title', 'Contact — ' . ($site->trans('site_name') ?? 'WhatsAppBizAI'))
@section('meta_description', 'Contactez l\'équipe ' . ($site->trans('site_name') ?? 'WhatsAppBizAI') . '. Demandez une démo, posez vos questions ou obtenez du support.')
@section('canonical_url', url('/contact'))
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    @include('components.seo')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ $site->favicon_path ? asset('storage/' . $site->favicon_path) : asset('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/switchers.css') }}">
    <style>
        :root { --sky: #0ea5e9; --sky-dark: #0284c7; --dark: #0f172a; --gray: #64748b; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f0f9ff; color: var(--dark); }
        nav { position: fixed; top: 0; width: 100%; background: rgba(255,255,255,.95); backdrop-filter: blur(10px); border-bottom: 1px solid #e2e8f0; z-index: 100; padding: 0 24px; }
        .nav-inner { max-width: 1100px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; height: 64px; }
        .logo { font-size: 20px; font-weight: 800; color: var(--dark); text-decoration: none; }
        .logo span { color: var(--sky); }
        .nav-links { display: flex; gap: 8px; align-items: center; }
        .btn-nav { padding: 8px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; }
        .btn-outline { border: 1.5px solid var(--sky); color: var(--sky); }
        .btn-primary { background: var(--sky); color: #fff; }

        .contact-wrap { max-width: 600px; margin: 120px auto 80px; padding: 0 24px; }
        .contact-wrap h1 { font-size: 36px; font-weight: 800; margin-bottom: 8px; }
        .contact-wrap .sub { color: var(--gray); font-size: 16px; margin-bottom: 32px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 14px; font-weight: 600; margin-bottom: 6px; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 12px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-size: 15px; font-family: inherit; transition: border-color .2s; }
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus { outline: none; border-color: var(--sky); }
        .form-group textarea { resize: vertical; min-height: 120px; }
        .btn-submit { width: 100%; padding: 14px; background: var(--sky); color: #fff; border: none; border-radius: 10px; font-size: 16px; font-weight: 700; cursor: pointer; }
        .btn-submit:hover { background: var(--sky-dark); }
        .success-msg { background: #dcfce7; color: #166534; padding: 14px 18px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; font-weight: 600; }
        .error-msg { background: #fef2f2; color: #991b1b; padding: 14px 18px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; }
        .info-cards { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 40px; }
        .info-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; }
        .info-card h3 { font-size: 14px; font-weight: 700; margin-bottom: 6px; }
        .info-card p { font-size: 13px; color: var(--gray); }
        .info-card a { color: var(--sky); text-decoration: none; font-weight: 600; }
        footer { background: var(--dark); padding: 32px 24px; text-align: center; border-top: 1px solid #1e293b; }
        footer p { font-size: 13px; color: #475569; }
        footer a { color: var(--sky); text-decoration: none; }
        @media (max-width: 640px) { .nav-links .btn-outline { display: none; } .info-cards { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<nav>
    <div class="nav-inner">
    @if($site->logo_path)
        <a href="{{ url('/') }}"><img src="{{ asset('storage/' . $site->logo_path) }}" alt="{{ $site->trans('site_name') ?? 'WhatsAppBizAI' }}" style="height:36px;"></a>
    @else
        @php $siteName = $site->trans('site_name') ?? 'WhatsAppBizAI'; $parts = explode('BizAI', $siteName); @endphp
        <a href="{{ url('/') }}" class="logo">{!! $parts[0] ?? $siteName !!}<span>{{ str_contains($siteName, 'BizAI') ? 'BizAI' : '' }}</span></a>
    @endif
        <div class="nav-links">
            <div class="switcher-wrap light-theme">
                <button class="switcher-btn lang-btn" data-lang="fr">FR</button>
                <button class="switcher-btn lang-btn" data-lang="en">EN</button>
            </div>
            <a href="{{ url('/') }}" class="btn-nav btn-outline">← {{ app()->getLocale() === 'fr' ? 'Accueil' : 'Home' }}</a>
            <a href="{{ url('login') }}" class="btn-nav btn-primary">{{ app()->getLocale() === 'fr' ? 'Connexion' : 'Login' }}</a>
        </div>
    </div>
</nav>

<div class="contact-wrap">
    @if(app()->getLocale() === 'fr')
        <h1>Contactez-nous</h1>
        <p class="sub">Une question, une démo ou un partenariat ? Écrivez-nous, nous répondons sous 24h.</p>
    @else
        <h1>Contact Us</h1>
        <p class="sub">A question, demo request or partnership? Write to us, we reply within 24 hours.</p>
    @endif

    @if(session('success'))
        <div class="success-msg">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="error-msg">
            <ul style="margin:0; padding-left:16px;">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ url('contact') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>{{ app()->getLocale() === 'fr' ? 'Votre nom' : 'Your name' }}</label>
            <input type="text" name="name" required placeholder="{{ app()->getLocale() === 'fr' ? 'Jean Dupont' : 'John Smith' }}">
        </div>
        <div class="form-group">
            <label>{{ app()->getLocale() === 'fr' ? 'Email' : 'Email' }}</label>
            <input type="email" name="email" required placeholder="{{ app()->getLocale() === 'fr' ? 'vous@entreprise.com' : 'you@business.com' }}">
        </div>
        <div class="form-group">
            <label>{{ app()->getLocale() === 'fr' ? 'Sujet' : 'Subject' }}</label>
            <select name="subject" required>
                @if(app()->getLocale() === 'fr')
                    <option value="demo">Demander une démo</option>
                    <option value="support">Support technique</option>
                    <option value="partnership">Partenariat</option>
                    <option value="other">Autre</option>
                @else
                    <option value="demo">Request a demo</option>
                    <option value="support">Technical support</option>
                    <option value="partnership">Partnership</option>
                    <option value="other">Other</option>
                @endif
            </select>
        </div>
        <div class="form-group">
            <label>{{ app()->getLocale() === 'fr' ? 'Message' : 'Message' }}</label>
            <textarea name="message" required placeholder="{{ app()->getLocale() === 'fr' ? 'Décrivez votre demande...' : 'Describe your request...' }}"></textarea>
        </div>
        <button type="submit" class="btn-submit">{{ app()->getLocale() === 'fr' ? 'Envoyer le message' : 'Send message' }}</button>
    </form>

    <div class="info-cards">
        <div class="info-card">
            <h3>📧 {{ app()->getLocale() === 'fr' ? 'Email' : 'Email' }}</h3>
            <p><a href="mailto:{{ $site->contact_email ?? 'contact@whatsappbizai.com' }}">{{ $site->contact_email ?? 'contact@whatsappbizai.com' }}</a></p>
        </div>
        <div class="info-card">
            <h3>⏰ {{ app()->getLocale() === 'fr' ? 'Temps de réponse' : 'Response time' }}</h3>
            <p>{{ app()->getLocale() === 'fr' ? 'Sous 24 heures en jours ouvrés' : 'Within 24 hours on business days' }}</p>
        </div>
        <div class="info-card">
            <h3>🌍 {{ app()->getLocale() === 'fr' ? 'Disponibilité' : 'Availability' }}</h3>
            <p>{{ app()->getLocale() === 'fr' ? 'Service disponible 24/7, support du lundi au vendredi' : 'Service available 24/7, support Monday to Friday' }}</p>
        </div>
        <div class="info-card">
            <h3>🔒 {{ app()->getLocale() === 'fr' ? 'Confidentialité' : 'Privacy' }}</h3>
            <p><a href="{{ url('privacy') }}">{{ app()->getLocale() === 'fr' ? 'Politique de confidentialité' : 'Privacy Policy' }}</a></p>
        </div>
    </div>
</div>

<footer>
    <p>{!! $site->trans('footer_copyright') ?? '© ' . date('Y') . ' WhatsAppBizAI' !!} · <a href="{{ url('/') }}">{{ app()->getLocale() === 'fr' ? 'Accueil' : 'Home' }}</a> · <a href="{{ url('privacy') }}">{{ app()->getLocale() === 'fr' ? 'Confidentialité' : 'Privacy' }}</a> · <a href="{{ url('terms') }}">{{ app()->getLocale() === 'fr' ? 'Conditions' : 'Terms' }}</a> · <a href="{{ url('contact') }}">{{ app()->getLocale() === 'fr' ? 'Contact' : 'Contact' }}</a></p>
</footer>

<script>
window.__langSwitchUrl = '{{ url("language") }}';
window.__i18n = {
    fr: {!! json_encode([
        'nav' => trans('app.nav', [], 'fr'),
        'landing' => trans('app.landing', [], 'fr'),
    ]) !!},
    en: {!! json_encode([
        'nav' => trans('app.nav', [], 'en'),
        'landing' => trans('app.landing', [], 'en'),
    ]) !!}
};
</script>
<script src="{{ asset('js/preferences.js') }}?v={{ time() }}"></script>

</body>
</html>
