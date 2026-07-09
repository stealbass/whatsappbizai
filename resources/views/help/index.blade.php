@extends('help.layout')

@section('meta_title', app()->getLocale()==='en' ? 'Help Center — WhatsAppBizAI' : 'Centre d\'aide — WhatsAppBizAI')
@section('meta_description', app()->getLocale()==='en'
    ? 'Find answers, tutorials and guides to get the most out of WhatsAppBizAI.'
    : 'Trouvez des réponses, tutoriels et guides pour tirer le meilleur de WhatsAppBizAI.')

@section('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "{{ app()->getLocale()==='en' ? 'Help Center' : 'Centre d\'aide' }} — {{ $site->trans('site_name') ?? 'WhatsAppBizAI' }}",
  "description": "{{ app()->getLocale()==='en' ? 'Find answers, tutorials and guides.' : 'Trouvez des réponses, tutoriels et guides.' }}",
  "url": "{{ url('help') }}",
  "publisher": {
    "@type": "Organization",
    "name": "{{ $site->trans('site_name') ?? 'WhatsAppBizAI' }}",
    "url": "{{ url('/') }}"
  }
}
</script>
@endsection

@section('content')
{{-- HERO --}}
<div class="help-hero">
    <div class="badge">{{ app()->getLocale()==='en' ? '📚 Help Center' : '📚 Centre d\'aide' }}</div>
    <h1>{{ app()->getLocale()==='en' ? 'How can we <span>help you?</span>' : 'Comment pouvons-nous <span>vous aider ?</span>' }}</h1>
    <p>{{ app()->getLocale()==='en'
        ? 'Browse our articles, tutorials and step-by-step guides to master WhatsAppBizAI.'
        : 'Parcourez nos articles, tutoriels et guides pas à pas pour maîtriser WhatsAppBizAI.' }}</p>

    <form action="{{ url('help/search') }}" method="GET" class="search-bar">
        <input type="text" name="q" placeholder="{{ app()->getLocale()==='en' ? 'Search for a topic, feature or question...' : 'Rechercher un sujet, une fonctionnalité, une question...' }}" autocomplete="off" autofocus>
        <button type="submit">🔍</button>
    </form>
</div>

{{-- LAYOUT --}}
<div class="help-layout">
    {{-- SIDEBAR --}}
    <aside class="help-sidebar">
        <p class="sidebar-title">{{ app()->getLocale()==='en' ? 'Categories' : 'Catégories' }}</p>
        <a href="{{ url('help') }}" class="sidebar-cat active">
            <span class="cat-icon">🏠</span>
            {{ app()->getLocale()==='en' ? 'All topics' : 'Tous les sujets' }}
        </a>
        @foreach($categories as $cat)
            <a href="{{ url('help/'.$cat->slug) }}" class="sidebar-cat">
                <span class="cat-icon">{{ $cat->icon }}</span>
                {{ $cat->trans('name') }}
                <span class="cat-count">{{ $cat->published_articles_count }}</span>
            </a>
        @endforeach
    </aside>

    {{-- MAIN --}}
    <main class="help-main">
        {{-- CATEGORIES GRID --}}
        <h2 style="font-size:22px;font-weight:800;margin-bottom:20px;">
            {{ app()->getLocale()==='en' ? 'Browse by category' : 'Parcourir par catégorie' }}
        </h2>
        <div class="cats-grid">
            @foreach($categories as $cat)
                <a href="{{ url('help/'.$cat->slug) }}" class="cat-card">
                    <div class="cat-card-icon" style="background:{{ $cat->color }}1a;">
                        {{ $cat->icon }}
                    </div>
                    <h3>{{ $cat->trans('name') }}</h3>
                    <p>{{ $cat->trans('description') }}</p>
                    <div class="cat-card-footer">
                        <span>{{ $cat->published_articles_count }} {{ app()->getLocale()==='en' ? 'articles' : 'articles' }}</span>
                        <span style="color:var(--sky);">→</span>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- FEATURED --}}
        @if($featured->count())
        <h2 style="font-size:22px;font-weight:800;margin:40px 0 20px;">
            {{ app()->getLocale()==='en' ? '⭐ Popular articles' : '⭐ Articles populaires' }}
        </h2>
        <div class="articles-list">
            @foreach($featured as $art)
                <a href="{{ url('help/article/'.$art->slug) }}" class="art-card">
                    <div class="art-card-icon">{{ $art->type_icon }}</div>
                    <div class="art-card-body">
                        <h3>{{ $art->trans('title') }}</h3>
                        <p>{{ $art->trans('excerpt') ?? \Illuminate\Support\Str::limit(strip_tags($art->trans('content')),120) }}</p>
                        <div class="art-card-meta">
                            <span class="badge-type badge-{{ $art->type }}">{{ $art->type_label }}</span>
                            @if($art->difficulty)
                                <span class="badge-difficulty" style="color:{{ $art->difficulty_color }}">{{ $art->difficulty_label }}</span>
                            @endif
                            <span style="color:#94a3b8;">{{ $art->reading_time }} min</span>
                            @if($art->category)
                                <span style="color:#94a3b8;">· {{ $art->category->trans('name') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="art-card-arrow">→</div>
                </a>
            @endforeach
        </div>
        @endif
    </main>
</div>
@endsection
