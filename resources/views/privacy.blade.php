@section('meta_title', 'Politique de Confidentialité — WhatsAppBizAI')
@section('meta_description', 'Politique de confidentialité de WhatsAppBizAI. Protection des données personnelles, cookies et droits des utilisateurs conforme au RGPD.')
@section('canonical_url', url('/privacy'))
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    @include('components.seo')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ $site->favicon_path ? asset('storage/' . $site->favicon_path) : asset('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/switchers.css') }}">
    <style>
        :root { --sky: #0ea5e9; --dark: #0f172a; --gray: #64748b; --light: #f8fafc; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #fff; color: var(--dark); line-height: 1.7; }
        nav { position: fixed; top: 0; width: 100%; background: rgba(255,255,255,.95); backdrop-filter: blur(10px); border-bottom: 1px solid #e2e8f0; z-index: 100; padding: 0 24px; }
        .nav-inner { max-width: 1100px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; height: 64px; }
        .logo { font-size: 20px; font-weight: 800; color: var(--dark); text-decoration: none; }
        .logo span { color: var(--sky); }
        .nav-links { display: flex; gap: 8px; align-items: center; }
        .btn-nav { padding: 8px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; }
        .btn-outline { border: 1.5px solid var(--sky); color: var(--sky); }
        .btn-primary { background: var(--sky); color: #fff; }
        .content { max-width: 780px; margin: 120px auto 80px; padding: 0 24px; }
        .content h1 { font-size: 36px; font-weight: 800; margin-bottom: 8px; }
        .content .date { color: var(--gray); font-size: 14px; margin-bottom: 40px; }
        .content h2 { font-size: 22px; font-weight: 700; margin: 36px 0 12px; }
        .content p, .content li { font-size: 16px; color: #334155; margin-bottom: 14px; }
        .content ul { padding-left: 24px; }
        footer { background: var(--dark); padding: 32px 24px; text-align: center; border-top: 1px solid #1e293b; }
        footer p { font-size: 13px; color: #475569; }
        footer a { color: var(--sky); text-decoration: none; }
        @media (max-width: 640px) { .nav-links .btn-outline { display: none; } }
    </style>
    @include('components.custom-code')
</head>
<body>

<nav>
    <div class="nav-inner">
        @php $siteName = $site->trans('site_name') ?? 'WhatsAppBizAI'; $parts = explode('BizAI', $siteName); @endphp
        <a href="{{ url('/') }}" class="logo">{!! $parts[0] ?? $siteName !!}<span>{{ str_contains($siteName, 'BizAI') ? 'BizAI' : '' }}</span></a>
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

<div class="content">
    @if($site->trans('privacy_policy'))
        <h1>{{ app()->getLocale() === 'fr' ? 'Politique de confidentialité' : 'Privacy Policy' }}</h1>
        <p class="date">{{ app()->getLocale() === 'fr' ? 'Dernière mise à jour' : 'Last updated' }} : {{ $site->updated_at->format('d M Y') }}</p>
        {!! $site->trans('privacy_policy') !!}
    @else
    @if(app()->getLocale() === 'fr')
        <h1>Politique de confidentialité</h1>
        <p class="date">Dernière mise à jour : 5 juillet 2026</p>

        <h2>1. Introduction</h2>
        <p>WhatsAppBizAI respecte votre vie privée. Cette politique explique quelles données nous collectons, pourquoi, et comment vous pouvez exercer vos droits.</p>

        <h2>2. Données collectées</h2>
        <ul>
            <li><strong>Compte :</strong> nom, nom d'entreprise, email, ville, mot de passe (chiffré).</li>
            <li><strong>WhatsApp Business :</strong> numéro de téléphone, token d'API Meta.</li>
            <li><strong>Données clients :</strong> contacts, conversations, devis et factures que vous gérez via l'outil.</li>
            <li><strong>Messages IA :</strong> historique des conversations avec l'agent IA pour assurer la continuité du service.</li>
            <li><strong>Paiements :</strong> identifiants de transaction Flutterwave. Nous ne stockons PAS vos données bancaires.</li>
        </ul>

        <h2>3. Utilisation des données</h2>
        <p>Vos données sont utilisées uniquement pour :</p>
        <ul>
            <li>Fournir et améliorer nos services.</li>
            <li>Envoyer les devis, factures et relances que vous configurez.</li>
            <li>Assurer le support client.</li>
            <li>Respecter nos obligations légales.</li>
        </ul>

        <h2>4. Partage des données</h2>
        <p>Nous ne vendons jamais vos données. Elles ne sont partagées qu'avec :</p>
        <ul>
            <li><strong>Meta/WhatsApp :</strong> pour l'envoi des messages (API WhatsApp Business).</li>
            <li><strong>Google Gemini :</strong> pour le traitement IA des conversations.</li>
            <li><strong>Flutterwave :</strong> pour le traitement sécurisé des paiements.</li>
            <li><strong>Hébergeur :</strong> serveurs sécurisés pour le stockage des données.</li>
        </ul>

        <h2>5. Sécurité</h2>
        <p>Vos données sont protégées par chiffrement TLS/SSL en transit et au repos. Votre mot de passe est haché avec bcrypt. Nous appliquons les bonnes pratiques de sécurité OWASP.</p>

        <h2>6. Conservation des données</h2>
        <p>Les données de votre compte sont conservées tant que votre compte est actif. Après suppression, elles sont effacées sous 30 jours, sauf obligation légale contraire.</p>

        <h2>7. Cookies</h2>
        <p>Nous utilisons des cookies strictement nécessaires au fonctionnement du site (session, préférences de langue/devise). Aucun cookie publicitaire ou de tracking tiers n'est utilisé.</p>

        <h2>8. Vos droits</h2>
        <p>Conformément au RGPD et aux lois locales, vous avez le droit de :</p>
        <ul>
            <li>Accéder à vos données personnelles.</li>
            <li>Les corriger ou les supprimer.</li>
            <li>Vous opposer à leur traitement.</li>
            <li>Demander la portabilité de vos données.</li>
        </ul>
        <p>Pour exercer ces droits, contactez-nous à <a href="mailto:privacy@whatsappbizai.com">privacy@whatsappbizai.com</a>.</p>

        <h2>9. Contact</h2>
        <p>Pour toute question : <a href="mailto:privacy@whatsappbizai.com">privacy@whatsappbizai.com</a></p>
    @else
        <h1>Privacy Policy</h1>
        <p class="date">Last updated: July 5, 2026</p>

        <h2>1. Introduction</h2>
        <p>WhatsAppBizAI respects your privacy. This policy explains what data we collect, why, and how you can exercise your rights.</p>

        <h2>2. Data collected</h2>
        <ul>
            <li><strong>Account:</strong> name, business name, email, city, password (encrypted).</li>
            <li><strong>WhatsApp Business:</strong> phone number, Meta API token.</li>
            <li><strong>Customer data:</strong> contacts, conversations, quotes and invoices you manage through the tool.</li>
            <li><strong>AI messages:</strong> conversation history with the AI agent to ensure service continuity.</li>
            <li><strong>Payments:</strong> Flutterwave transaction IDs. We do NOT store your banking details.</li>
        </ul>

        <h2>3. Data usage</h2>
        <p>Your data is used solely to:</p>
        <ul>
            <li>Provide and improve our services.</li>
            <li>Send the quotes, invoices and reminders you configure.</li>
            <li>Provide customer support.</li>
            <li>Comply with legal obligations.</li>
        </ul>

        <h2>4. Data sharing</h2>
        <p>We never sell your data. It is only shared with:</p>
        <ul>
            <li><strong>Meta/WhatsApp:</strong> for sending messages (WhatsApp Business API).</li>
            <li><strong>Google Gemini:</strong> for AI processing of conversations.</li>
            <li><strong>Flutterwave:</strong> for secure payment processing.</li>
            <li><strong>Hosting provider:</strong> secure servers for data storage.</li>
        </ul>

        <h2>5. Security</h2>
        <p>Your data is protected by TLS/SSL encryption in transit and at rest. Your password is hashed with bcrypt. We follow OWASP security best practices.</p>

        <h2>6. Data retention</h2>
        <p>Account data is kept as long as your account is active. After deletion, data is erased within 30 days, unless required by law.</p>

        <h2>7. Cookies</h2>
        <p>We use strictly necessary cookies for site functionality (session, language/currency preferences). No advertising or third-party tracking cookies are used.</p>

        <h2>8. Your rights</h2>
        <p>Under GDPR and local laws, you have the right to:</p>
        <ul>
            <li>Access your personal data.</li>
            <li>Correct or delete it.</li>
            <li>Object to its processing.</li>
            <li>Request data portability.</li>
        </ul>
        <p>To exercise these rights, contact us at <a href="mailto:privacy@whatsappbizai.com">privacy@whatsappbizai.com</a>.</p>

        <h2>9. Contact</h2>
        <p>For any questions: <a href="mailto:privacy@whatsappbizai.com">privacy@whatsappbizai.com</a></p>
    @endif
    @endif
</div>

<footer>
    <p>{!! $site->trans('footer_copyright') ?? '© ' . date('Y') . ' WhatsAppBizAI' !!} · <a href="{{ url('/') }}">{{ app()->getLocale() === 'fr' ? 'Accueil' : 'Home' }}</a> · <a href="{{ url('privacy') }}">{{ app()->getLocale() === 'fr' ? 'Confidentialité' : 'Privacy' }}</a> · <a href="{{ url('terms') }}">{{ app()->getLocale() === 'fr' ? 'Conditions' : 'Terms' }}</a> · <a href="{{ url('contact') }}">{{ app()->getLocale() === 'fr' ? 'Contact' : 'Contact' }}</a></p>
</footer>

<script>
window.__langSwitchUrl = '{{ url("language") }}';
window.__i18n = {
    fr: {!! json_encode(['nav' => trans('app.nav', [], 'fr'), 'landing' => trans('app.landing', [], 'fr')]) !!},
    en: {!! json_encode(['nav' => trans('app.nav', [], 'en'), 'landing' => trans('app.landing', [], 'en')]) !!}
};
</script>
<script src="{{ asset('js/preferences.js') }}?v={{ time() }}"></script>

</body>
</html>
