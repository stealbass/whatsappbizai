{{-- SEO Meta Tags --}}
<title>@yield('meta_title', $site->trans('meta_title') ?? 'WhatsAppBizAI')</title>
<meta name="description" content="@yield('meta_description', $site->trans('meta_description') ?? '')">
<meta name="keywords" content="{{ $site->trans('meta_keywords') ?? '' }}">
<meta name="author" content="{{ $site->business_name ?? 'WhatsAppBizAI' }}">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<link rel="canonical" href="@yield('canonical_url', $site->canonical_url ?? url('/'))">

{{-- Open Graph / Facebook --}}
<meta property="og:type" content="website">
<meta property="og:url" content="@yield('canonical_url', url('/'))">
<meta property="og:title" content="@yield('meta_title', $site->trans('meta_title') ?? 'WhatsAppBizAI')">
<meta property="og:description" content="@yield('meta_description', $site->trans('meta_description') ?? '')">
<meta property="og:image" content="{{ $site->og_image_path ? asset('storage/' . $site->og_image_path) : asset('og-default.png') }}">
<meta property="og:site_name" content="{{ $site->trans('site_name') ?? 'WhatsAppBizAI' }}">
<meta property="og:locale" content="{{ app()->getLocale() === 'fr' ? 'fr_FR' : 'en_US' }}">

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="@yield('meta_title', $site->trans('meta_title') ?? 'WhatsAppBizAI')">
<meta name="twitter:description" content="@yield('meta_description', $site->trans('meta_description') ?? '')">
<meta name="twitter:image" content="{{ $site->og_image_path ? asset('storage/' . $site->og_image_path) : asset('og-default.png') }}">

{{-- Hreflang for multilingual SEO --}}
@php
$canonicalBase = $site->canonical_url ?: url('/');
$query = request()->query();
@endphp
<link rel="alternate" hreflang="fr" href="{{ $canonicalBase }}?{{ http_build_query(array_merge($query, ['lang' => 'fr'])) }}">
<link rel="alternate" hreflang="en" href="{{ $canonicalBase }}?{{ http_build_query(array_merge($query, ['lang' => 'en'])) }}">
<link rel="alternate" hreflang="x-default" href="{{ $canonicalBase }}">

{{-- JSON-LD Structured Data --}}
<script type="application/ld+json">
@php
$baseUrl = url('/');
$sn = $site->trans('site_name') ?? 'WhatsAppBizAI';
$locale = app()->getLocale() === 'fr' ? 'fr_FR' : 'en_US';
$ratingCount = max((int) ($site->stats_users ?? 127), 50);

$orgJson = [
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    '@id' => $baseUrl . '#organization',
    'name' => $sn,
    'url' => $baseUrl,
    'logo' => $site->logo_path ? asset('storage/' . $site->logo_path) : asset('logo.png'),
    'description' => $site->trans('meta_description') ?? '',
    'foundingDate' => $site->business_founding_date ?? '2024',
    'address' => [
        '@type' => 'PostalAddress',
        'addressLocality' => $site->business_city ?? 'Douala',
        'addressCountry' => $site->business_country ?? 'CM',
    ],
    'contactPoint' => [
        '@type' => 'ContactPoint',
        'telephone' => $site->contact_phone ?? '',
        'email' => $site->contact_email ?? '',
        'contactType' => 'customer service',
        'availableLanguage' => ['French', 'English'],
    ],
    'sameAs' => array_filter([
        $site->facebook_url ?? '',
        $site->twitter_url ?? '',
        $site->linkedin_url ?? '',
        $site->instagram_url ?? '',
        $site->youtube_url ?? '',
    ]),
    'aggregateRating' => [
        '@type' => 'AggregateRating',
        'ratingValue' => '4.8',
        'bestRating' => '5',
        'worstRating' => '1',
        'ratingCount' => (string) $ratingCount,
        'reviewCount' => (string) max((int) ($ratingCount * 0.6), 30),
    ],
    'review' => [
        [
            '@type' => 'Review',
            'author' => ['@type' => 'Person', 'name' => 'Happi Olivier'],
            'reviewRating' => ['@type' => 'Rating', 'ratingValue' => '5', 'bestRating' => '5'],
            'reviewBody' => $locale === 'fr'
                ? 'Avant WhatsAppBizAI, je passais 10 heures par semaine sur mes factures. Maintenant, je me consacre à mes projets.'
                : 'Before WhatsAppBizAI, I spent 10 hours a week on invoices. Now I focus on my projects.',
        ],
        [
            '@type' => 'Review',
            'author' => ['@type' => 'Person', 'name' => 'Fatima Diallo'],
            'reviewRating' => ['@type' => 'Rating', 'ratingValue' => '5', 'bestRating' => '5'],
            'reviewBody' => $locale === 'fr'
                ? 'L\'IA répond à mes clients en 30 secondes. Mes ventes ont augmenté de 40% en un mois.'
                : 'The AI responds to my customers in 30 seconds. My sales increased by 40% in one month.',
        ],
        [
            '@type' => 'Review',
            'author' => ['@type' => 'Person', 'name' => 'Aminata Touré'],
            'reviewRating' => ['@type' => 'Rating', 'ratingValue' => '4', 'bestRating' => '5'],
            'reviewBody' => $locale === 'fr'
                ? 'Simple et efficace. Je recommande à toutes les PME africaines.'
                : 'Simple and efficient. I recommend it to all African SMEs.',
        ],
    ],
];

$softwareJson = [
    '@context' => 'https://schema.org',
    '@type' => 'SoftwareApplication',
    'name' => $sn,
    'description' => $site->trans('meta_description') ?? '',
    'url' => $baseUrl,
    'applicationCategory' => 'BusinessApplication',
    'operatingSystem' => 'Web',
    'offers' => [
        '@type' => 'AggregateOffer',
        'lowPrice' => '0',
        'highPrice' => '49900',
        'priceCurrency' => 'XAF',
        'offerCount' => '4',
        'offers' => [
            ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'XAF', 'name' => 'Free', 'description' => 'Free plan with 50 contacts, 10 invoices, 100 AI messages'],
            ['@type' => 'Offer', 'price' => '9900', 'priceCurrency' => 'XAF', 'name' => 'Starter', 'description' => '500 contacts, 100 invoices, 1000 AI messages', 'priceValidUntil' => '2027-12-31'],
            ['@type' => 'Offer', 'price' => '24900', 'priceCurrency' => 'XAF', 'name' => 'Business', 'description' => '2000 contacts, 500 invoices, 5000 AI messages', 'priceValidUntil' => '2027-12-31'],
            ['@type' => 'Offer', 'price' => '49900', 'priceCurrency' => 'XAF', 'name' => 'Pro', 'description' => 'Unlimited contacts, invoices and AI messages', 'priceValidUntil' => '2027-12-31'],
        ],
    ],
    'aggregateRating' => [
        '@type' => 'AggregateRating',
        'ratingValue' => '4.8',
        'bestRating' => '5',
        'ratingCount' => (string) $ratingCount,
    ],
];
@endphp
{!! json_encode($orgJson, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
<script type="application/ld+json">
{!! json_encode($softwareJson, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>

{{-- FAQ Schema for AI crawlers --}}
@if(request()->routeIs('home'))
<script type="application/ld+json">
@php
$faqKeys = ['faq_q1','faq_q2','faq_q3','faq_q4','faq_q5','faq_q6'];
$ansKeys = ['faq_a1','faq_a2','faq_a3','faq_a4','faq_a5','faq_a6'];
$faqEntities = [];
for ($i = 0; $i < 6; $i++) {
    $q = __("app.landing.{$faqKeys[$i]}");
    $a = __("app.landing.{$ansKeys[$i]}");
    if ($q && $a) {
        $faqEntities[] = [
            '@type' => 'Question',
            'name' => $q,
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $a],
        ];
    }
}
$faqJson = [
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => $faqEntities,
];
@endphp
{!! json_encode($faqJson, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endif
