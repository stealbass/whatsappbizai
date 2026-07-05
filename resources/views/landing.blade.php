@section('meta_title', 'WhatsAppBizAI — Agent IA WhatsApp pour PME | Devis, Factures, CRM Automatisés')
@section('meta_description', 'Automatisez votre back-office avec un agent IA sur WhatsApp. Devis instantanés PDF, facturation automatique, relances et CRM pour PME en Afrique. Essai gratuit sans carte bancaire.')
@section('canonical_url', url('/'))
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    @include('components.seo')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/switchers.css') }}">
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

        .hero { padding: 140px 24px 80px; text-align: center; background: linear-gradient(180deg, #f0f9ff 0%, #fff 100%); }
        .hero-badge { display: inline-flex; align-items: center; gap: 8px; background: #dcfce7; color: #166534; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; margin-bottom: 24px; }
        .hero h1 { font-size: clamp(32px, 5vw, 56px); font-weight: 900; line-height: 1.1; max-width: 800px; margin: 0 auto 20px; }
        .hero h1 span { color: var(--sky); }
        .hero p { font-size: 18px; color: var(--gray); max-width: 580px; margin: 0 auto 36px; line-height: 1.7; }
        .hero-cta { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }
        .btn-lg { padding: 14px 28px; border-radius: 10px; font-size: 16px; font-weight: 700; text-decoration: none; }
        .btn-lg.primary { background: var(--sky); color: #fff; box-shadow: 0 4px 20px rgba(14,165,233,.35); }
        .btn-lg.primary:hover { background: var(--sky-dark); }
        .btn-lg.ghost { border: 2px solid #e2e8f0; color: var(--dark); }

        .trust-bar { display: flex; justify-content: center; gap: 32px; flex-wrap: wrap; margin-top: 48px; padding: 0 24px; }
        .trust-item { display: flex; align-items: center; gap: 8px; font-size: 14px; color: var(--gray); font-weight: 500; }
        .trust-item svg { width: 20px; height: 20px; color: var(--green); flex-shrink: 0; }

        .mockup-wrap { max-width: 900px; margin: 60px auto 0; padding: 0 24px; }
        .mockup { background: var(--dark); border-radius: 16px; padding: 24px; box-shadow: 0 30px 60px rgba(0,0,0,.2); overflow: hidden; }
        .mockup-header { display: flex; gap: 8px; margin-bottom: 20px; }
        .dot { width: 12px; height: 12px; border-radius: 50%; }
        .dot-r { background: #ef4444; } .dot-y { background: #f59e0b; } .dot-g { background: #22c55e; }
        .chat { display: flex; flex-direction: column; gap: 12px; }
        .msg { max-width: 75%; padding: 10px 14px; border-radius: 12px; font-size: 14px; line-height: 1.5; }
        .msg.in { background: #1e293b; color: #e2e8f0; border-bottom-left-radius: 2px; }
        .msg.out { background: var(--sky); color: #fff; border-bottom-right-radius: 2px; align-self: flex-end; }
        .msg.ai { background: #065f46; color: #d1fae5; border-bottom-right-radius: 2px; align-self: flex-end; }
        .msg-label { font-size: 10px; opacity: .6; margin-bottom: 3px; }

        .section { padding: 80px 24px; }
        .section-inner { max-width: 1100px; margin: 0 auto; }
        .section-title { text-align: center; margin-bottom: 56px; }
        .section-title h2 { font-size: 36px; font-weight: 800; margin-bottom: 12px; }
        .section-title p { font-size: 17px; color: var(--gray); max-width: 520px; margin: 0 auto; }
        .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; }
        .feature-card { background: var(--light); border: 1px solid #e2e8f0; border-radius: 14px; padding: 28px; transition: box-shadow .2s; }
        .feature-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,.08); }
        .feature-icon { font-size: 32px; margin-bottom: 14px; }
        .feature-card h3 { font-size: 17px; font-weight: 700; margin-bottom: 8px; }
        .feature-card p { font-size: 14px; color: var(--gray); line-height: 1.6; }

        .how-it-works { background: var(--light); }
        .steps { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 32px; max-width: 900px; margin: 0 auto; }
        .step { text-align: center; }
        .step-num { width: 56px; height: 56px; background: var(--sky); color: #fff; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 800; margin-bottom: 16px; }
        .step h3 { font-size: 17px; font-weight: 700; margin-bottom: 8px; }
        .step p { font-size: 14px; color: var(--gray); line-height: 1.6; }

        .testimonials { background: #fff; }
        .testimonial-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; }
        .testimonial-card { background: var(--light); border: 1px solid #e2e8f0; border-radius: 14px; padding: 28px; }
        .testimonial-card .stars { color: #f59e0b; font-size: 18px; margin-bottom: 12px; }
        .testimonial-card blockquote { font-size: 15px; color: #334155; line-height: 1.7; margin-bottom: 16px; font-style: italic; }
        .testimonial-card .author { display: flex; align-items: center; gap: 12px; }
        .testimonial-card .avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--sky); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 16px; }
        .testimonial-card .author-name { font-size: 14px; font-weight: 700; }
        .testimonial-card .author-role { font-size: 13px; color: var(--gray); }

        .pricing { background: var(--light); }
        .pricing-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; max-width: 1000px; margin: 0 auto; }
        .plan { background: #fff; border: 2px solid #e2e8f0; border-radius: 14px; padding: 28px; text-align: center; }
        .plan.popular { border-color: var(--sky); position: relative; }
        .plan.popular::before { content: '⭐ Populaire'; position: absolute; top: -14px; left: 50%; transform: translateX(-50%); background: var(--sky); color: #fff; padding: 4px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; white-space: nowrap; }
        .plan h3 { font-size: 18px; font-weight: 800; margin-bottom: 8px; }
        .plan .price { font-size: 36px; font-weight: 900; color: var(--sky); margin: 12px 0; }
        .plan .price span { font-size: 14px; font-weight: 400; color: var(--gray); }
        .plan ul { list-style: none; margin: 20px 0 24px; text-align: left; }
        .plan ul li { font-size: 14px; color: var(--gray); padding: 5px 0; }
        .plan ul li::before { content: '✓ '; color: #22c55e; font-weight: 700; }
        .plan .btn-plan { display: block; padding: 11px; border-radius: 8px; font-weight: 700; font-size: 14px; text-decoration: none; text-align: center; }
        .plan .btn-plan.sky { background: var(--sky); color: #fff; }
        .plan .btn-plan.outline { border: 2px solid #e2e8f0; color: var(--dark); }

        .stats { background: var(--dark); padding: 60px 24px; text-align: center; }
        .stats-inner { max-width: 900px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 32px; }
        .stat h3 { font-size: 42px; font-weight: 900; color: var(--sky); }
        .stat p { font-size: 14px; color: #94a3b8; margin-top: 4px; }

        .contact-section { background: #fff; }
        .contact-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 48px; max-width: 900px; margin: 0 auto; align-items: start; }
        .contact-info h3 { font-size: 20px; font-weight: 700; margin-bottom: 16px; }
        .contact-info p { font-size: 15px; color: var(--gray); line-height: 1.7; margin-bottom: 20px; }
        .contact-detail { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; font-size: 14px; }
        .contact-detail-icon { width: 36px; height: 36px; background: #e0f2fe; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 18px; }
        .contact-form { background: var(--light); border: 1px solid #e2e8f0; border-radius: 14px; padding: 28px; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 5px; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 10px 12px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 14px; font-family: inherit; }
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus { outline: none; border-color: var(--sky); }
        .form-group textarea { resize: vertical; min-height: 90px; }
        .btn-submit { width: 100%; padding: 12px; background: var(--sky); color: #fff; border: none; border-radius: 8px; font-size: 15px; font-weight: 700; cursor: pointer; }
        .btn-submit:hover { background: var(--sky-dark); }
        .success-msg { background: #dcfce7; color: #166534; padding: 12px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; font-weight: 600; text-align: center; }

        .cta-section { padding: 80px 24px; text-align: center; background: linear-gradient(135deg, #0ea5e9 0%, #0f172a 100%); }
        .cta-section h2 { font-size: 36px; font-weight: 800; color: #fff; margin-bottom: 14px; }
        .cta-section p { font-size: 17px; color: #bae6fd; margin-bottom: 32px; }
        .btn-white { background: #fff; color: var(--sky); padding: 14px 32px; border-radius: 10px; font-size: 16px; font-weight: 700; text-decoration: none; display: inline-block; }

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

        .cookie-bar { position: fixed; bottom: 0; left: 0; right: 0; background: var(--dark); color: #e2e8f0; padding: 16px 24px; z-index: 200; display: flex; align-items: center; justify-content: center; gap: 16px; flex-wrap: wrap; font-size: 14px; }
        .cookie-bar a { color: var(--sky); text-decoration: underline; }
        .cookie-bar button { padding: 8px 20px; border-radius: 8px; border: none; font-weight: 700; cursor: pointer; font-size: 13px; }
        .cookie-accept { background: var(--sky); color: #fff; }
        .cookie-dismiss { background: #334155; color: #94a3b8; }

        @media (max-width: 640px) {
            .nav-links .btn-outline { display: none; }
            .hero h1 { font-size: 30px; }
            .hero p { font-size: 16px; }
            .contact-grid { grid-template-columns: 1fr; }
            .footer-inner { grid-template-columns: 1fr 1fr; }
            .footer-bottom { flex-direction: column; text-align: center; }
        }
    </style>
</head>
<body>

<!-- COOKIE BANNER -->
<div class="cookie-bar" id="cookieBar" style="display:none;">
    <span>🍪 <span data-t-key="landing.cookie_text">Nous utilisons des cookies essentiels au fonctionnement du site. En continuant, vous acceptez notre</span> <a href="{{ url('privacy') }}" data-t-key="landing.cookie_link">politique de confidentialité</a>.</span>
    <button class="cookie-accept" onclick="acceptCookies()" data-t-key="landing.cookie_accept">Accepter</button>
    <button class="cookie-dismiss" onclick="dismissCookies()" data-t-key="landing.cookie_dismiss">En savoir plus</button>
</div>

<!-- NAV -->
<nav>
    <div class="nav-inner">
        <a href="{{ url('/') }}" class="logo">WhatsApp<span>BizAI</span></a>
        <div class="nav-links">
            <div class="switcher-wrap light-theme">
                <button class="switcher-btn lang-btn" data-lang="fr">FR</button>
                <button class="switcher-btn lang-btn" data-lang="en">EN</button>
                <div class="switcher-sep"></div>
                <button class="switcher-btn currency-btn" data-currency="XAF">XAF</button>
                <button class="switcher-btn currency-btn" data-currency="USD">USD</button>
                <button class="switcher-btn currency-btn" data-currency="EUR">EUR</button>
            </div>
            <a href="{{ url('contact') }}" class="btn-nav btn-outline" data-t-key="nav.contact">Contact</a>
            <a href="{{ url('register') }}" class="btn-nav btn-outline" data-t-key="nav.free_trial">Essai gratuit</a>
            <a href="{{ url('login') }}" class="btn-nav btn-primary" data-t-key="nav.login">Connexion</a>
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="hero-badge">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <span data-t-key="landing.hero_badge">Prêt à l'emploi · Des milliers d'utilisateurs dans le monde</span>
    </div>
    <h1><span data-t-html="landing.hero_title_1">Votre back-office complet</span><br><span data-t-html="landing.hero_title_2">opéré par l'IA sur WhatsApp</span></h1>
    <p data-t-key="landing.hero_desc">Devis, factures, relances et support client gérés automatiquement par l'IA. Sans changer vos habitudes.</p>
    <div class="hero-cta">
        <a href="{{ url('register') }}" class="btn-lg primary" data-t-key="landing.cta_start">🚀 Commencer gratuitement</a>
        <a href="#demo" class="btn-lg ghost" data-t-key="landing.cta_demo">Voir la démo</a>
    </div>
    <div class="trust-bar">
        <div class="trust-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span data-t-key="landing.trust_1">Aucune carte bancaire requise</span>
        </div>
        <div class="trust-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span data-t-key="landing.trust_2">Configuration en 5 minutes</span>
        </div>
        <div class="trust-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span data-t-key="landing.trust_3">Support par email</span>
        </div>
        <div class="trust-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span data-t-key="landing.trust_4">RGPD conforme</span>
        </div>
    </div>
</section>

<!-- MOCKUP DEMO -->
<div class="mockup-wrap" id="demo">
    <div class="mockup">
        <div class="mockup-header">
            <div class="dot dot-r"></div>
            <div class="dot dot-y"></div>
            <div class="dot dot-g"></div>
        </div>
        <div class="chat">
            <div class="msg in">
                <div class="msg-label" data-t-key="landing.demo_client">Marie N. · 09:14</div>
                <span data-t-key="landing.demo_msg1">Bonjour, j'aurais besoin d'un site web pour mon salon de coiffure avec une boutique en ligne. C'est combien ?</span>
            </div>
            <div class="msg ai">
                <div class="msg-label"><span data-t-key="landing.demo_ai">🤖 Agent IA</span> · 09:14</div>
                <span data-t-html="landing.demo_reply1">Bonjour Marie ! 😊 Nous proposons un site e-commerce complet avec boutique en ligne à partir de 450 000 FCFA (forfait tout inclus).<br><br>Cela inclut : design sur mesure, catalogue produits, paiement en ligne et livraison. Délai : 3 à 4 semaines.<br><br>Souhaitez-vous que je vous prépare un devis détaillé ?</span>
            </div>
            <div class="msg in"><span data-t-key="landing.demo_msg2">Oui s'il vous plaît !</span></div>
            <div class="msg ai">
                <div class="msg-label"><span data-t-key="landing.demo_ai">🤖 Agent IA</span> · 09:15</div>
                <span data-t-key="landing.demo_reply2">Parfait ! Je prépare votre devis et je vous l'envoie dans quelques instants en PDF. 📄</span>
            </div>
            <div class="msg out" style="background:#1e293b; color:#94a3b8; font-size:12px;">
                <span data-t-key="landing.demo_doc">📄 <strong>Devis DEV-2026-0042.pdf</strong></span><br>
                <span data-t-key="landing.demo_doc_desc">Site e-commerce · 450 000 XAF · Valable 15 jours</span>
            </div>
        </div>
    </div>
</div>

<!-- FEATURES -->
<section class="section">
    <div class="section-inner">
        <div class="section-title">
            <h2 data-t-key="landing.features_title">Tout ce dont votre PME a besoin</h2>
            <p data-t-key="landing.features_desc">Un seul outil, opéré par l'IA, accessible depuis votre WhatsApp existant.</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🤖</div>
                <h3 data-t-key="landing.f1_title">Agent IA 24/7</h3>
                <p data-t-key="landing.f1_desc">Répond aux clients, qualifie les prospects et présente vos services en français et en anglais, même la nuit.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📄</div>
                <h3 data-t-key="landing.f2_title">Devis instantanés</h3>
                <p data-t-key="landing.f2_desc">L'IA génère et envoie un devis PDF professionnel directement dans la conversation WhatsApp en 30 secondes.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🧾</div>
                <h3 data-t-key="landing.f3_title">Facturation automatique</h3>
                <p data-t-key="landing.f3_desc">Créez et envoyez des factures PDF à vos clients sans quitter votre dashboard. Suivi des paiements intégré.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔔</div>
                <h3 data-t-key="landing.f4_title">Relances automatiques</h3>
                <p data-t-key="landing.f4_desc">Les factures en retard sont relancées automatiquement par WhatsApp chaque matin. Sans vous en soucier.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3 data-t-key="landing.f5_title">CRM intégré</h3>
                <p data-t-key="landing.f5_desc">Tous vos clients, leurs historiques de commandes et conversations centralisés dans un dashboard clair.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📣</div>
                <h3 data-t-key="landing.f6_title">Broadcast marketing</h3>
                <p data-t-key="landing.f6_desc">Envoyez des messages promotionnels personnalisés à tous vos clients ou prospects en un clic.</p>
            </div>
        </div>
    </div>
</section>

<!-- HOW IT WORKS -->
<section class="section how-it-works">
    <div class="section-inner">
        <div class="section-title">
            <h2 data-t-key="landing.how_title">Comment ça marche</h2>
            <p data-t-key="landing.how_desc">En 3 étapes simples, transformez WhatsApp en outil de back-office intelligent.</p>
        </div>
        <div class="steps">
            <div class="step">
                <div class="step-num">1</div>
                <h3 data-t-key="landing.step1_title">Créez votre compte</h3>
                <p data-t-key="landing.step1_desc">Inscrivez-vous gratuitement en 2 minutes. Aucune carte bancaire requise.</p>
            </div>
            <div class="step">
                <div class="step-num">2</div>
                <h3 data-t-key="landing.step2_title">Configurez votre IA</h3>
                <p data-t-key="landing.step2_desc">Ajoutez vos services, tarifs et paramètres. L'IA apprend votre activité.</p>
            </div>
            <div class="step">
                <div class="step-num">3</div>
                <h3 data-t-key="landing.step3_title">Activez WhatsApp</h3>
                <p data-t-key="landing.step3_desc">Connectez votre numéro WhatsApp Business. Votre agent IA est en ligne.</p>
            </div>
        </div>
    </div>
</section>

<!-- TESTIMONIALS -->
<section class="section testimonials">
    <div class="section-inner">
        <div class="section-title">
            <h2 data-t-key="landing.testimonials_title">Ils nous font confiance</h2>
            <p data-t-key="landing.testimonials_desc">Des entrepreneurs et PME dans le monde entier utilisent WhatsAppBizAI au quotidien.</p>
        </div>
        <div class="testimonial-grid">
            <div class="testimonial-card">
                <div class="stars">★★★★★</div>
                <blockquote data-t-key="landing.t1_quote">"Depuis que j'utilise WhatsAppBizAI, mes clients reçoivent leurs devis en 30 secondes. J'ai gagné 3h par jour sur l'administratif."</blockquote>
                <div class="author">
                    <div class="avatar">FA</div>
                    <div>
                        <div class="author-name" data-t-key="landing.t1_name">Fatima A.</div>
                        <div class="author-role" data-t-key="landing.t1_role">Agence web, Douala</div>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="stars">★★★★★</div>
                <blockquote data-t-key="landing.t2_quote">"L'agent IA répond à mes clients même la nuit. Les relances automatiques ont réduit mes impayés de 40% en un mois."</blockquote>
                <div class="author">
                    <div class="avatar">KN</div>
                    <div>
                        <div class="author-name" data-t-key="landing.t2_name">Kofi N.</div>
                        <div class="author-role" data-t-key="landing.t2_role">Coach professionnel, Accra</div>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="stars">★★★★★</div>
                <blockquote data-t-key="landing.t3_quote">"Setup en 5 minutes, facturation automatisée, CRM intégré. C'est exactement ce dont mon salon avait besoin."</blockquote>
                <div class="author">
                    <div class="avatar">SL</div>
                    <div>
                        <div class="author-name" data-t-key="landing.t3_name">Sophie L.</div>
                        <div class="author-role" data-t-key="landing.t3_role">Salon de beauté, Abidjan</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- STATS -->
<div class="stats">
    <div class="stats-inner">
        <div class="stat"><h3>&lt; 30s</h3><p data-t-key="landing.stat_response">Délai de réponse IA moyen</p></div>
        <div class="stat"><h3>24/7</h3><p data-t-key="landing.stat_avail">Disponibilité de l'agent</p></div>
        <div class="stat"><h3>FR + EN</h3><p data-t-key="landing.stat_langs">Langues supportées</p></div>
        <div class="stat"><h3>100%</h3><p data-t-key="landing.stat_secure">Sécurisé &amp; conforme RGPD</p></div>
    </div>
</div>

<!-- PRICING -->
<section class="section pricing">
    <div class="section-inner">
        <div class="section-title">
            <h2 data-t-key="landing.pricing_title">Tarifs simples et transparents</h2>
            <p data-t-key="landing.pricing_desc">Commencez gratuitement. Passez à un plan payant quand vous êtes prêt.</p>
        </div>
        <div class="pricing-grid">
            <div class="plan">
                <h3 data-t-key="pricing.plan_free">Gratuit</h3>
                <div class="price" data-xaf="0" data-period="monthly">0 <span>XAF/mois</span></div>
                <ul>
                    <li>1 numéro WhatsApp</li>
                    <li>50 messages IA/mois</li>
                    <li>5 devis/mois</li>
                    <li>5 factures/mois</li>
                </ul>
                <a href="{{ url('register') }}" class="btn-plan outline" data-t-key="pricing.get_started">Commencer</a>
            </div>
            <div class="plan popular">
                <h3 data-t-key="pricing.plan_starter">Starter</h3>
                <div class="price" data-xaf="9900" data-period="monthly">9 900 <span>XAF/mois</span></div>
                <ul>
                    <li>1 numéro WhatsApp</li>
                    <li>500 messages IA/mois</li>
                    <li>Devis & factures illimités</li>
                    <li>Relances automatiques</li>
                </ul>
                <a href="{{ url('register') }}" class="btn-plan sky" data-t-key="pricing.get_started">Commencer</a>
            </div>
            <div class="plan">
                <h3 data-t-key="pricing.plan_business">Business</h3>
                <div class="price" data-xaf="24900" data-period="monthly">24 900 <span>XAF/mois</span></div>
                <ul>
                    <li>Illimité</li>
                    <li>Messages IA illimités</li>
                    <li>Broadcast marketing</li>
                    <li>Multi-utilisateurs</li>
                    <li>Support prioritaire</li>
                </ul>
                <a href="{{ url('register') }}" class="btn-plan sky" data-t-key="pricing.get_started">Commencer</a>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <h2 data-t-key="landing.cta_title">Prêt à automatiser votre PME ?</h2>
    <p data-t-key="landing.cta_desc">Rejoignez les entrepreneurs qui gèrent leur back-office sur WhatsApp avec l'IA.</p>
    <a href="{{ url('register') }}" class="btn-white" data-t-key="landing.cta_btn">Créer mon compte gratuitement →</a>
</section>

<!-- CONTACT -->
<section class="section contact-section" id="contact">
    <div class="section-inner">
        <div class="section-title">
            <h2 data-t-key="landing.contact_title">Une question ? Contactez-nous</h2>
            <p data-t-key="landing.contact_desc">Notre équipe vous répond sous 24 heures en jours ouvrés.</p>
        </div>
        <div class="contact-grid">
            <div class="contact-info">
                <h3 data-t-key="landing.contact_info_title">Parlons de votre projet</h3>
                <p data-t-key="landing.contact_info_desc">Que vous soyez intéressé par une démo, un partenariat ou simplement une question technique, nous sommes là pour vous.</p>
                <div class="contact-detail">
                    <div class="contact-detail-icon">📧</div>
                    <div>
                        <strong>Email</strong><br>
                        <a href="mailto:contact@whatsappbizai.com">contact@whatsappbizai.com</a>
                    </div>
                </div>
                <div class="contact-detail">
                    <div class="contact-detail-icon">💬</div>
                    <div>
                        <strong>WhatsApp</strong><br>
                        <span data-t-key="landing.contact_whatsapp">Disponible pour les clients existants</span>
                    </div>
                </div>
                <div class="contact-detail">
                    <div class="contact-detail-icon">⏰</div>
                    <div>
                        <strong data-t-key="landing.contact_hours">Horaires</strong><br>
                        <span data-t-key="landing.contact_hours_value">Lundi - Vendredi, 9h - 18h (GMT+1)</span>
                    </div>
                </div>
            </div>
            <div class="contact-form">
                @if(session('contact_success'))
                    <div class="success-msg">{{ session('contact_success') }}</div>
                @endif
                <form action="{{ url('contact') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label data-t-key="landing.form_name">Votre nom</label>
                        <input type="text" name="name" required data-t-placeholder="landing.form_name_placeholder" placeholder="Jean Dupont">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required data-t-placeholder="landing.form_email_placeholder" placeholder="vous@entreprise.com">
                    </div>
                    <div class="form-group">
                        <label data-t-key="landing.form_subject">Sujet</label>
                        <select name="subject" required data-t-options="landing.form_opts">
                            <option value="demo">Demander une démo</option>
                            <option value="support">Support technique</option>
                            <option value="partnership">Partenariat</option>
                            <option value="other">Autre</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label data-t-key="landing.form_message">Message</label>
                        <textarea name="message" required data-t-placeholder="landing.form_msg_placeholder" placeholder="Décrivez votre demande..."></textarea>
                    </div>
                    <button type="submit" class="btn-submit" data-t-key="landing.form_submit">Envoyer le message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- FAQ — SEO & AI Crawl -->
<section id="faq" style="padding:80px 24px;background:#fff;">
    <div style="max-width:800px;margin:0 auto;">
        <h2 style="text-align:center;font-size:32px;font-weight:800;margin-bottom:12px;" data-t-key="landing.faq_title">Questions fréquentes</h2>
        <p style="text-align:center;color:var(--gray);margin-bottom:48px;font-size:16px;" data-t-key="landing.faq_desc">Tout ce que vous devez savoir sur WhatsAppBizAI.</p>

        <div style="display:flex;flex-direction:column;gap:16px;">
            <details style="border:1px solid #e2e8f0;border-radius:12px;padding:20px 24px;cursor:pointer;">
                <summary style="font-weight:700;font-size:16px;list-style:none;display:flex;justify-content:space-between;align-items:center;" data-t-key="landing.faq_q1">Qu'est-ce que WhatsAppBizAI ?</summary>
                <p style="margin-top:12px;color:var(--gray);line-height:1.7;" data-t-key="landing.faq_a1">WhatsAppBizAI est un back-office intelligent pour PME qui utilise un agent IA sur WhatsApp. Il automatise la création de devis PDF, la facturation, les relances de paiement et le support client, le tout depuis votre WhatsApp existant.</p>
            </details>

            <details style="border:1px solid #e2e8f0;border-radius:12px;padding:20px 24px;cursor:pointer;">
                <summary style="font-weight:700;font-size:16px;list-style:none;display:flex;justify-content:space-between;align-items:center;" data-t-key="landing.faq_q2">Combien coûte WhatsAppBizAI ?</summary>
                <p style="margin-top:12px;color:var(--gray);line-height:1.7;" data-t-key="landing.faq_a2">WhatsAppBizAI propose un plan gratuit pour démarrer, sans carte bancaire requise. Les plans Starter et Business sont disponibles à partir de 4 900 XAF/mois pour accéder à plus de contacts, factures et messages IA.</p>
            </details>

            <details style="border:1px solid #e2e8f0;border-radius:12px;padding:20px 24px;cursor:pointer;">
                <summary style="font-weight:700;font-size:16px;list-style:none;display:flex;justify-content:space-between;align-items:center;" data-t-key="landing.faq_q3">Comment l'agent IA répond-il aux clients ?</summary>
                <p style="margin-top:12px;color:var(--gray);line-height:1.7;" data-t-key="landing.faq_a3">L'agent IA utilise Google Gemini pour générer des réponses contextualisées. Il connaît votre catalogue de services, vos tarifs et vos instructions personnalisées. Il répond en français ou en anglais selon la langue du client, 24h/24.</p>
            </details>

            <details style="border:1px solid #e2e8f0;border-radius:12px;padding:20px 24px;cursor:pointer;">
                <summary style="font-weight:700;font-size:16px;list-style:none;display:flex;justify-content:space-between;align-items:center;" data-t-key="landing.faq_q4">Faut-il modifier mon numéro WhatsApp ?</summary>
                <p style="margin-top:12px;color:var(--gray);line-height:1.7;" data-t-key="landing.faq_a4">Non. WhatsAppBizAI utilise l'API officielle de WhatsApp Business. Vous connectez simplement votre numéro WhatsApp Business existant via l'interface d'administration. Aucune modification technique n'est requise.</p>
            </details>

            <details style="border:1px solid #e2e8f0;border-radius:12px;padding:20px 24px;cursor:pointer;">
                <summary style="font-weight:700;font-size:16px;list-style:none;display:flex;justify-content:space-between;align-items:center;" data-t-key="landing.faq_q5">Mes données sont-elles sécurisées ?</summary>
                <p style="margin-top:12px;color:var(--gray);line-height:1.7;" data-t-key="landing.faq_a5">Oui. WhatsAppBizAI est conforme au RGPD. Vos données et celles de vos clients sont stockées de manière sécurisée et ne sont jamais partagées avec des tiers. Vous pouvez supprimer vos données à tout moment.</p>
            </details>

            <details style="border:1px solid #e2e8f0;border-radius:12px;padding:20px 24px;cursor:pointer;">
                <summary style="font-weight:700;font-size:16px;list-style:none;display:flex;justify-content:space-between;align-items:center;" data-t-key="landing.faq_q6">Dans quels pays est disponible WhatsAppBizAI ?</summary>
                <p style="margin-top:12px;color:var(--gray);line-height:1.7;" data-t-key="landing.faq_a6">WhatsAppBizAI est disponible dans tous les pays africains où WhatsApp est utilisé : Cameroun, Sénégal, Côte d'Ivoire, Nigeria, Ghana, Kenya, Maroc, et bien d'autres. L'agent IA supporte le français et l'anglais.</p>
            </details>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <div class="footer-inner">
        <div class="footer-brand">
            <a href="{{ url('/') }}" class="logo">WhatsApp<span>BizAI</span></a>
            <p data-t-key="landing.footer_desc">Back-office intelligent pour PME. Gérez devis, factures et relances directement depuis WhatsApp grâce à l'IA.</p>
        </div>
        <div class="footer-col">
            <h4 data-t-key="landing.footer_product">Produit</h4>
            <a href="#demo" data-t-key="nav.demo">Démo</a>
            <a href="{{ url('pricing') }}" data-t-key="nav.pricing">Tarifs</a>
            <a href="{{ url('register') }}" data-t-key="nav.register">S'inscrire</a>
            <a href="{{ url('login') }}" data-t-key="nav.login">Connexion</a>
        </div>
        <div class="footer-col">
            <h4 data-t-key="landing.footer_company">Entreprise</h4>
            <a href="{{ url('contact') }}" data-t-key="nav.contact">Contact</a>
            <a href="mailto:contact@whatsappbizai.com">Email</a>
        </div>
        <div class="footer-col">
            <h4 data-t-key="landing.footer_legal">Légal</h4>
            <a href="{{ url('privacy') }}" data-t-key="nav.privacy">Confidentialité</a>
            <a href="{{ url('terms') }}" data-t-key="nav.terms">Conditions</a>
        </div>
    </div>
    <div class="footer-bottom">
        <p>© {{ date('Y') }} WhatsAppBizAI. <span data-t-key="landing.footer_all_rights">Tous droits réservés.</span></p>
        <div>
            <a href="{{ url('privacy') }}" data-t-key="nav.privacy">Confidentialité</a> ·
            <a href="{{ url('terms') }}" data-t-key="nav.terms">Conditions</a> ·
            <a href="{{ url('contact') }}" data-t-key="nav.contact">Contact</a>
        </div>
    </div>
</footer>

<script>
window.__i18n = {
    fr: {!! json_encode([
        'nav' => trans('app.nav', [], 'fr'),
        'landing' => trans('app.landing', [], 'fr'),
        'pricing' => trans('app.pricing', [], 'fr'),
        'footer' => trans('app.footer', [], 'fr'),
        'per_month' => trans('app.per_month', [], 'fr'),
        'per_year' => trans('app.per_year', [], 'fr'),
    ]) !!},
    en: {!! json_encode([
        'nav' => trans('app.nav', [], 'en'),
        'landing' => trans('app.landing', [], 'en'),
        'pricing' => trans('app.pricing', [], 'en'),
        'footer' => trans('app.footer', [], 'en'),
        'per_month' => trans('app.per_month', [], 'en'),
        'per_year' => trans('app.per_year', [], 'en'),
    ]) !!}
};
</script>
<script src="{{ asset('js/preferences.js') }}?v={{ time() }}"></script>

<script>
// Cookie consent
function acceptCookies() {
    localStorage.setItem('wbai_cookies', 'accepted');
    document.getElementById('cookieBar').style.display = 'none';
}
function dismissCookies() {
    window.location.href = '{{ url('privacy') }}';
}
window.addEventListener('load', function() {
    if (!localStorage.getItem('wbai_cookies')) {
        document.getElementById('cookieBar').style.display = 'flex';
    }
});
</script>

</body>
</html>
