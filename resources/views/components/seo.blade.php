{{-- SEO Meta Tags --}}
<title>@yield('meta_title', $site->meta_title ?? 'WhatsAppBizAI')</title>
<meta name="description" content="@yield('meta_description', $site->meta_description ?? '')">
<meta name="keywords" content="{{ $site->meta_keywords ?? '' }}">
<meta name="author" content="{{ $site->business_name ?? 'WhatsAppBizAI' }}">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<link rel="canonical" href="@yield('canonical_url', $site->canonical_url ?? url('/'))">

{{-- Open Graph / Facebook --}}
<meta property="og:type" content="website">
<meta property="og:url" content="@yield('canonical_url', url('/'))">
<meta property="og:title" content="@yield('meta_title', $site->meta_title ?? 'WhatsAppBizAI')">
<meta property="og:description" content="@yield('meta_description', $site->meta_description ?? '')">
<meta property="og:image" content="{{ $site->og_image_path ? asset('storage/' . $site->og_image_path) : asset('og-default.png') }}">
<meta property="og:site_name" content="{{ $site->site_name ?? 'WhatsAppBizAI' }}">
<meta property="og:locale" content="{{ app()->getLocale() === 'fr' ? 'fr_FR' : 'en_US' }}">

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="@yield('meta_title', $site->meta_title ?? 'WhatsAppBizAI')">
<meta name="twitter:description" content="@yield('meta_description', $site->meta_description ?? '')">
<meta name="twitter:image" content="{{ $site->og_image_path ? asset('storage/' . $site->og_image_path) : asset('og-default.png') }}">

{{-- Hreflang for multilingual --}}
<link rel="alternate" hreflang="fr" href="{{ url('/'). '?' . http_build_query(array_merge(request()->query(), ['lang' => 'fr'])) }}">
<link rel="alternate" hreflang="en" href="{{ url('/'). '?' . http_build_query(array_merge(request()->query(), ['lang' => 'en'])) }}">
<link rel="alternate" hreflang="x-default" href="{{ url('/') }}">

{{-- JSON-LD Structured Data --}}
<script type="application/ld+json">
@php
$baseUrl = url('/');
$ldJson = [
    '@context' => 'https://schema.org',
    '@type' => 'SoftwareApplication',
    'name' => $site->site_name ?? 'WhatsAppBizAI',
    'description' => $site->meta_description ?? '',
    'url' => $baseUrl,
    'applicationCategory' => 'BusinessApplication',
    'operatingSystem' => 'Web',
    'offers' => [
        '@type' => 'Offer',
        'price' => '0',
        'priceCurrency' => 'XAF',
        'description' => 'Free plan available',
    ],
    'author' => [
        '@type' => 'Organization',
        'name' => $site->business_name ?? 'WhatsAppBizAI',
        'url' => $baseUrl,
        'address' => [
            '@type' => 'PostalAddress',
            'addressLocality' => $site->business_city ?? 'Douala',
            'addressCountry' => $site->business_country ?? 'CM',
        ],
    ],
    'aggregateRating' => [
        '@type' => 'AggregateRating',
        'ratingValue' => '4.8',
        'ratingCount' => (string) ($site->stats_users ?? 100),
    ],
];
@endphp
{!! json_encode($ldJson, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>

{{-- FAQ Schema for AI crawlers --}}
@if(request()->routeIs('home'))
<script type="application/ld+json">
@php
$faqJson = [
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => [
        ['@type' => 'Question', 'name' => 'Qu\'est-ce que WhatsAppBizAI ?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'WhatsAppBizAI est un back-office intelligent pour PME qui utilise un agent IA sur WhatsApp. Il automatise la création de devis PDF, la facturation, les relances de paiement et le support client.']],
        ['@type' => 'Question', 'name' => 'Combien coûte WhatsAppBizAI ?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'WhatsAppBizAI propose un plan gratuit pour démarrer, sans carte bancaire requise. Les plans Starter et Business sont disponibles à partir de 4 900 XAF/mois.']],
        ['@type' => 'Question', 'name' => 'Comment l\'agent IA répond-il aux clients ?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'L\'agent IA utilise Google Gemini pour générer des réponses contextualisées. Il connaît votre catalogue de services, vos tarifs et vos instructions personnalisées. Il répond 24h/24.']],
        ['@type' => 'Question', 'name' => 'Mes données sont-elles sécurisées ?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Oui. WhatsAppBizAI est conforme au RGPD. Vos données sont stockées de manière sécurisée et ne sont jamais partagées avec des tiers.']],
    ],
];
@endphp
{!! json_encode($faqJson, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endif
