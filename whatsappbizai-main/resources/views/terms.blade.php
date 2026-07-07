@section('meta_title', 'Conditions Générales d\'Utilisation — WhatsAppBizAI')
@section('meta_description', 'Conditions générales d\'utilisation de WhatsAppBizAI. Droits et obligations des utilisateurs, conditions d\'abonnement et de paiement.')
@section('canonical_url', url('/terms'))
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
</head>
<body>

<nav>
    <div class="nav-inner">
        <a href="{{ url('/') }}" class="logo">{{ $site->site_name ?? 'WhatsApp' }}<span>{{ $site->site_name ? '' : 'BizAI' }}</span></a>
        <div class="nav-links">
            <a href="{{ url('/') }}" class="btn-nav btn-outline">← {{ app()->getLocale() === 'fr' ? 'Accueil' : 'Home' }}</a>
            <a href="{{ url('login') }}" class="btn-nav btn-primary">{{ app()->getLocale() === 'fr' ? 'Connexion' : 'Login' }}</a>
        </div>
    </div>
</nav>

<div class="content">
    @if(app()->getLocale() === 'fr')
        <h1>Conditions d'utilisation</h1>
        <p class="date">Dernière mise à jour : 5 juillet 2026</p>

        <h2>1. Acceptation des conditions</h2>
        <p>En utilisant WhatsAppBizAI, vous acceptez ces conditions d'utilisation. Si vous n'acceptez pas, veillez ne pas utiliser le service.</p>

        <h2>2. Description du service</h2>
        <p>WhatsAppBizAI est un outil de back-office intelligent pour PME, accessible via WhatsApp. Il comprend :</p>
        <ul>
            <li>Un agent IA qui répond à vos clients sur WhatsApp.</li>
            <li>La génération automatique de devis et factures PDF.</li>
            <li>Les relances automatiques de factures impayées.</li>
            <li>Un CRM et un tableau de bord de suivi.</li>
            <li>Des outils de marketing par messagerie.</li>
        </ul>

        <h2>3. Inscription et compte</h2>
        <p>Vous devez fournir des informations exactes lors de l'inscription. Vous êtes responsable de la sécurité de votre compte et de votre mot de passe.</p>

        <h2>4. Utilisation acceptable</h2>
        <p>Vous vous engagez à :</p>
        <ul>
            <li>Ne pas utiliser le service à des fins illégales ou frauduleuses.</li>
            <li>Ne pas envoyer de spam ou de messages non sollicités.</li>
            <li>Ne pas tenter de contourner les mesures de sécurité.</li>
            <li>Respecter les lois locales et internationales sur la protection des données.</li>
        </ul>

        <h2>5. Tarifs et paiement</h2>
        <p>Les tarifs sont disponibles sur notre page <a href="{{ url('pricing') }}">Tarifs</a>. Les abonnements payants sont facturés mensuellement via Flutterwave. L'annulation est possible à tout moment.</p>

        <h2>6. Propriété intellectuelle</h2>
        <p>Le code, le design et le contenu de WhatsAppBizAI sont protégés par le droit d'auteur. Vous conservez la propriété de vos données clients et contenus que vous importez.</p>

        <h2>7. Limitation de responsabilité</h2>
        <p>WhatsAppBizAI est fourni "en l'état". Nous ne garantissons pas l'absence d'interruption ou d'erreur. Notre responsabilité est limitée au montant payé au cours des 12 derniers mois.</p>

        <h2>8. Résiliation</h2>
        <p>Vous pouvez supprimer votre compte à tout moment depuis votre dashboard. Nous pouvons suspendre votre compte en cas de violation de ces conditions.</p>

        <h2>9. Modification des conditions</h2>
        <p>Nous nous réservons le droit de modifier ces conditions. Les changements significatifs vous seront notifiés par email ou via l'application.</p>

        <h2>10. Contact</h2>
        <p>Pour toute question : <a href="mailto:legal@whatsappbizai.com">legal@whatsappbizai.com</a></p>
    @else
        <h1>Terms of Service</h1>
        <p class="date">Last updated: July 5, 2026</p>

        <h2>1. Acceptance of terms</h2>
        <p>By using WhatsAppBizAI, you agree to these terms of service. If you do not agree, please do not use the service.</p>

        <h2>2. Description of service</h2>
        <p>WhatsAppBizAI is an intelligent back-office tool for SMEs, accessible via WhatsApp. It includes:</p>
        <ul>
            <li>An AI agent that responds to your customers on WhatsApp.</li>
            <li>Automatic generation of PDF quotes and invoices.</li>
            <li>Automatic reminders for unpaid invoices.</li>
            <li>A CRM and tracking dashboard.</li>
            <li>Messaging marketing tools.</li>
        </ul>

        <h2>3. Registration and account</h2>
        <p>You must provide accurate information when registering. You are responsible for the security of your account and password.</p>

        <h2>4. Acceptable use</h2>
        <p>You agree to:</p>
        <ul>
            <li>Not use the service for illegal or fraudulent purposes.</li>
            <li>Not send spam or unsolicited messages.</li>
            <li>Not attempt to bypass security measures.</li>
            <li>Comply with local and international data protection laws.</li>
        </ul>

        <h2>5. Pricing and payment</h2>
        <p>Pricing is available on our <a href="{{ url('pricing') }}">Pricing</a> page. Paid subscriptions are billed monthly via Flutterwave. Cancellation is possible at any time.</p>

        <h2>6. Intellectual property</h2>
        <p>The code, design and content of WhatsAppBizAI are protected by copyright. You retain ownership of your customer data and content you import.</p>

        <h2>7. Limitation of liability</h2>
        <p>WhatsAppBizAI is provided "as is". We do not guarantee the absence of interruptions or errors. Our liability is limited to the amount paid in the last 12 months.</p>

        <h2>8. Termination</h2>
        <p>You can delete your account at any time from your dashboard. We may suspend your account in case of violation of these terms.</p>

        <h2>9. Changes to terms</h2>
        <p>We reserve the right to modify these terms. Significant changes will be notified to you by email or via the application.</p>

        <h2>10. Contact</h2>
        <p>For any questions: <a href="mailto:legal@whatsappbizai.com">legal@whatsappbizai.com</a></p>
    @endif
</div>

<footer>
    <p>{!! $site->footer_copyright ?? '© ' . date('Y') . ' WhatsAppBizAI' !!} · <a href="{{ url('/') }}">{{ app()->getLocale() === 'fr' ? 'Accueil' : 'Home' }}</a> · <a href="{{ url('privacy') }}">{{ app()->getLocale() === 'fr' ? 'Confidentialité' : 'Privacy' }}</a> · <a href="{{ url('terms') }}">{{ app()->getLocale() === 'fr' ? 'Conditions' : 'Terms' }}</a> · <a href="{{ url('contact') }}">{{ app()->getLocale() === 'fr' ? 'Contact' : 'Contact' }}</a></p>
</footer>

</body>
</html>
