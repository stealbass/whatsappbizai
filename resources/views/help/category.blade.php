@extends('help.layout')

@section('meta_title', $category->trans('name'))
@section('meta_description', $category->trans('description'))

@section('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "CollectionPage",
  "name": "{{ $category->trans('name') }} — {{ app()->getLocale()==='en'?'Help Center':'Centre d\'aide' }}",
  "description": "{{ $category->trans('description') }}",
  "url": "{{ url('help/'.$category->slug) }}",
  "breadcrumb": {
    "@type": "BreadcrumbList",
    "itemListElement": [
      {"@type":"ListItem","position":1,"name":"{{ app()->getLocale()==='en'?'Home':'Accueil' }}","item":"{{ url('/') }}"},
      {"@type":"ListItem","position":2,"name":"{{ app()->getLocale()==='en'?'Help Center':'Centre d\'aide' }}","item":"{{ url('help') }}"},
      {"@type":"ListItem","position":3,"name":"{{ $category->trans('name') }}","item":"{{ url('help/'.$category->slug) }}"}
    ]
  }
}
</script>
@endsection

@section('content')
<div class="help-layout no-hero">
    {{-- SIDEBAR --}}
    <aside class="help-sidebar">
        <p class="sidebar-title">{{ app()->getLocale()==='en' ? 'Categories' : 'Catégories' }}</p>
        <a href="{{ url('help') }}" class="sidebar-cat">
            <span class="cat-icon">🏠</span>
            {{ app()->getLocale()==='en' ? 'All topics' : 'Tous les sujets' }}
        </a>
        @foreach($allCategories as $cat)
            <a href="{{ url('help/'.$cat->slug) }}" class="sidebar-cat {{ $cat->id === $category->id ? 'active' : '' }}">
                <span class="cat-icon">{{ $cat->icon }}</span>
                {{ $cat->trans('name') }}
                <span class="cat-count">{{ $cat->publishedArticles()->count() }}</span>
            </a>
        @endforeach
    </aside>

    {{-- MAIN --}}
    <main class="help-main">
        {{-- Breadcrumb --}}
        <nav class="breadcrumb">
            <a href="{{ url('help') }}">{{ app()->getLocale()==='en'?'Help Center':'Centre d\'aide' }}</a>
            <span style="margin:0 6px;">›</span>
            <span>{{ $category->trans('name') }}</span>
        </nav>

        {{-- Category Header --}}
        <div style="display:flex;align-items:center;gap:18px;margin-bottom:32px;">
            <div style="font-size:44px;width:72px;height:72px;background:{{ $category->color }}1a;border-radius:18px;display:flex;align-items:center;justify-content:center;">
                {{ $category->icon }}
            </div>
            <div>
                <h1 style="font-size:28px;font-weight:900;margin-bottom:6px;">{{ $category->trans('name') }}</h1>
                <p style="font-size:15px;color:var(--gray);">{{ $category->trans('description') }}</p>
            </div>
        </div>

        {{-- ARTICLES --}}
        @if($articles->count())
        <div style="margin-bottom:40px;">
            <h2 style="font-size:19px;font-weight:800;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                <span>📄</span> {{ app()->getLocale()==='en' ? 'Articles' : 'Articles' }}
                <span style="font-size:13px;font-weight:500;color:var(--gray);background:#f1f5f9;padding:2px 10px;border-radius:20px;">{{ $articles->count() }}</span>
            </h2>
            <div class="articles-list">
                @foreach($articles as $art)
                    <a href="{{ url('help/article/'.$art->slug) }}" class="art-card">
                        <div class="art-card-icon">📄</div>
                        <div class="art-card-body">
                            <h3>{{ $art->trans('title') }}</h3>
                            <p>{{ $art->trans('excerpt') ?? \Illuminate\Support\Str::limit(strip_tags($art->trans('content')),120) }}</p>
                            <div class="art-card-meta">
                                <span class="badge-type badge-article">{{ app()->getLocale()==='en'?'Article':'Article' }}</span>
                                <span style="color:#94a3b8;">{{ $art->reading_time }} min</span>
                            </div>
                        </div>
                        <div class="art-card-arrow">→</div>
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- TUTORIALS --}}
        @if($tutorials->count())
        <div style="margin-bottom:40px;">
            <h2 style="font-size:19px;font-weight:800;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                <span>🎓</span> {{ app()->getLocale()==='en' ? 'Tutorials' : 'Tutoriels' }}
                <span style="font-size:13px;font-weight:500;color:var(--gray);background:#f1f5f9;padding:2px 10px;border-radius:20px;">{{ $tutorials->count() }}</span>
            </h2>
            <div class="articles-list">
                @foreach($tutorials as $art)
                    <a href="{{ url('help/article/'.$art->slug) }}" class="art-card">
                        <div class="art-card-icon">🎓</div>
                        <div class="art-card-body">
                            <h3>{{ $art->trans('title') }}</h3>
                            <p>{{ $art->trans('excerpt') ?? \Illuminate\Support\Str::limit(strip_tags($art->trans('content')),120) }}</p>
                            <div class="art-card-meta">
                                <span class="badge-type badge-tutorial">{{ app()->getLocale()==='en'?'Tutorial':'Tutoriel' }}</span>
                                @if($art->difficulty)
                                    <span class="badge-difficulty" style="color:{{ $art->difficulty_color }};">{{ $art->difficulty_label }}</span>
                                @endif
                                <span style="color:#94a3b8;">{{ $art->reading_time }} min</span>
                            </div>
                        </div>
                        <div class="art-card-arrow">→</div>
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- INTERACTIVE GUIDES --}}
        @if($guides->count())
        <div style="margin-bottom:40px;">
            <h2 style="font-size:19px;font-weight:800;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                <span>🗺️</span> {{ app()->getLocale()==='en' ? 'Interactive Guides' : 'Guides interactifs' }}
                <span style="font-size:13px;font-weight:500;color:var(--gray);background:#f1f5f9;padding:2px 10px;border-radius:20px;">{{ $guides->count() }}</span>
            </h2>
            <div class="articles-list">
                @foreach($guides as $art)
                    <a href="{{ url('help/article/'.$art->slug) }}" class="art-card">
                        <div class="art-card-icon">🗺️</div>
                        <div class="art-card-body">
                            <h3>{{ $art->trans('title') }}</h3>
                            <p>{{ $art->trans('excerpt') ?? \Illuminate\Support\Str::limit(strip_tags($art->trans('content')),120) }}</p>
                            <div class="art-card-meta">
                                <span class="badge-type badge-guide">{{ app()->getLocale()==='en'?'Interactive Guide':'Guide interactif' }}</span>
                                @if($art->difficulty)
                                    <span class="badge-difficulty" style="color:{{ $art->difficulty_color }};">{{ $art->difficulty_label }}</span>
                                @endif
                                <span style="color:#94a3b8;">{{ $art->reading_time }} min</span>
                            </div>
                        </div>
                        <div class="art-card-arrow">→</div>
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        @if($articles->isEmpty() && $tutorials->isEmpty() && $guides->isEmpty())
            <div style="text-align:center;padding:60px 24px;color:var(--gray);">
                <div style="font-size:48px;margin-bottom:12px;">📭</div>
                <p>{{ app()->getLocale()==='en' ? 'No content yet in this category.' : 'Aucun contenu dans cette catégorie pour l\'instant.' }}</p>
            </div>
        @endif
    </main>
</div>
@endsection
