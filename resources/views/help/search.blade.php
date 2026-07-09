@extends('help.layout')

@section('meta_title', app()->getLocale()==='en' ? 'Search — Help Center' : 'Recherche — Centre d\'aide')
@section('meta_description', app()->getLocale()==='en' ? 'Search results for: '.$q : 'Résultats de recherche pour : '.$q)

@section('content')
<div style="padding:110px 24px 40px;background:var(--light);text-align:center;">
    <h1 style="font-size:28px;font-weight:800;margin-bottom:14px;">
        {{ app()->getLocale()==='en' ? 'Search results' : 'Résultats de recherche' }}
    </h1>
    <form action="{{ url('help/search') }}" method="GET" class="search-bar" style="max-width:560px;margin:0 auto;">
        <input type="text" name="q" value="{{ e($q) }}"
               placeholder="{{ app()->getLocale()==='en' ? 'Search...' : 'Rechercher...' }}"
               autocomplete="off" autofocus>
        <button type="submit">🔍</button>
    </form>
    @if(strlen($q) >= 2)
        <p style="margin-top:14px;font-size:15px;color:var(--gray);">
            @if($results->count())
                {{ $results->count() }} {{ app()->getLocale()==='en' ? 'result(s) for' : 'résultat(s) pour' }}
                "<strong>{{ e($q) }}</strong>"
            @else
                {{ app()->getLocale()==='en' ? 'No results for' : 'Aucun résultat pour' }}
                "<strong>{{ e($q) }}</strong>"
            @endif
        </p>
    @endif
</div>

<div class="help-layout">
    {{-- SIDEBAR --}}
    <aside class="help-sidebar">
        <p class="sidebar-title">{{ app()->getLocale()==='en' ? 'Categories' : 'Catégories' }}</p>
        <a href="{{ url('help') }}" class="sidebar-cat">
            <span class="cat-icon">🏠</span>
            {{ app()->getLocale()==='en' ? 'All topics' : 'Tous les sujets' }}
        </a>
        @foreach($allCategories as $cat)
            <a href="{{ url('help/'.$cat->slug) }}" class="sidebar-cat">
                <span class="cat-icon">{{ $cat->icon }}</span>
                {{ $cat->trans('name') }}
            </a>
        @endforeach
    </aside>

    <main class="help-main">
        @if(strlen($q) < 2)
            <div style="text-align:center;padding:60px 0;color:var(--gray);">
                <div style="font-size:48px;margin-bottom:12px;">🔍</div>
                <p>{{ app()->getLocale()==='en' ? 'Enter at least 2 characters to search.' : 'Saisissez au moins 2 caractères pour rechercher.' }}</p>
            </div>
        @elseif($results->isEmpty())
            <div style="text-align:center;padding:60px 0;color:var(--gray);">
                <div style="font-size:48px;margin-bottom:12px;">😕</div>
                <p style="font-size:17px;font-weight:600;margin-bottom:8px;">
                    {{ app()->getLocale()==='en' ? 'No results found.' : 'Aucun résultat trouvé.' }}
                </p>
                <p style="font-size:14px;margin-bottom:24px;">
                    {{ app()->getLocale()==='en'
                        ? 'Try different keywords or browse our categories.'
                        : 'Essayez d\'autres mots-clés ou parcourez nos catégories.' }}
                </p>
                <a href="{{ url('help') }}" style="display:inline-block;padding:10px 24px;background:var(--sky);color:#fff;border-radius:10px;font-weight:600;text-decoration:none;">
                    {{ app()->getLocale()==='en' ? '← Back to Help Center' : '← Retour au Help Center' }}
                </a>
            </div>
        @else
            {{-- Group results by type --}}
            @php
                $byType = $results->groupBy('type');
                $typeOrder = ['article','tutorial','guide'];
            @endphp

            @foreach($typeOrder as $type)
                @if(isset($byType[$type]))
                    @php
                        $label = match($type) {
                            'tutorial' => app()->getLocale()==='en' ? '🎓 Tutorials' : '🎓 Tutoriels',
                            'guide'    => app()->getLocale()==='en' ? '🗺️ Interactive Guides' : '🗺️ Guides interactifs',
                            default    => '📄 Articles',
                        };
                    @endphp
                    <h2 style="font-size:17px;font-weight:800;margin-bottom:14px;margin-top:{{ $loop->first?'0':'32px' }};">
                        {{ $label }}
                        <span style="font-size:13px;font-weight:500;color:var(--gray);background:#f1f5f9;padding:2px 10px;border-radius:20px;margin-left:8px;">{{ $byType[$type]->count() }}</span>
                    </h2>
                    <div class="articles-list" style="margin-bottom:8px;">
                        @foreach($byType[$type] as $art)
                            <a href="{{ url('help/article/'.$art->slug) }}" class="art-card">
                                <div class="art-card-icon">{{ $art->type_icon }}</div>
                                <div class="art-card-body">
                                    <h3>{{ $art->trans('title') }}</h3>
                                    <p>{{ $art->trans('excerpt') ?? \Illuminate\Support\Str::limit(strip_tags($art->trans('content')),120) }}</p>
                                    <div class="art-card-meta">
                                        <span class="badge-type badge-{{ $type }}">{{ $art->type_label }}</span>
                                        @if($art->category)
                                            <span style="color:#94a3b8;">{{ $art->category->trans('name') }}</span>
                                        @endif
                                        <span style="color:#94a3b8;">{{ $art->reading_time }} min</span>
                                    </div>
                                </div>
                                <div class="art-card-arrow">→</div>
                            </a>
                        @endforeach
                    </div>
                @endif
            @endforeach
        @endif
    </main>
</div>
@endsection
