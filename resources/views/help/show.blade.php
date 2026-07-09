@extends('help.layout')

@section('meta_title', $article->trans('meta_title') ?? $article->trans('title'))
@section('meta_description', $article->trans('meta_description') ?? $article->trans('excerpt'))

@section('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "{{ $article->type === 'tutorial' ? 'HowTo' : 'Article' }}",
  "headline": "{{ e($article->trans('title')) }}",
  "description": "{{ e($article->trans('excerpt') ?? \Illuminate\Support\Str::limit(strip_tags($article->trans('content')),160)) }}",
  "url": "{{ url('help/article/'.$article->slug) }}",
  "datePublished": "{{ $article->published_at?->toIso8601String() }}",
  "dateModified": "{{ $article->updated_at->toIso8601String() }}",
  "author": {"@type":"Organization","name":"{{ e($article->author_name) }}"},
  "publisher": {"@type":"Organization","name":"{{ e($site->trans('site_name') ?? 'WhatsAppBizAI') }}","url":"{{ url('/') }}"},
  @if($article->featured_image)
  "image": "{{ asset('storage/'.$article->featured_image) }}",
  @endif
  "inLanguage": "{{ app()->getLocale() }}",
  "breadcrumb": {
    "@type": "BreadcrumbList",
    "itemListElement": [
      {"@type":"ListItem","position":1,"name":"{{ app()->getLocale()==='en'?'Home':'Accueil' }}","item":"{{ url('/') }}"},
      {"@type":"ListItem","position":2,"name":"Help Center","item":"{{ url('help') }}"},
      {"@type":"ListItem","position":3,"name":"{{ e($article->category->trans('name')) }}","item":"{{ url('help/'.$article->category->slug) }}"},
      {"@type":"ListItem","position":4,"name":"{{ e($article->trans('title')) }}","item":"{{ url('help/article/'.$article->slug) }}"}
    ]
  }
  @if($article->type === 'tutorial' && $article->steps)
  ,"step": [
    @foreach($article->steps as $i => $step)
    {
      "@type": "HowToStep",
      "position": {{ $i + 1 }},
      "name": "{{ e(app()->getLocale()==='en' ? ($step['title_en']??$step['title_fr']??'') : ($step['title_fr']??$step['title_en']??'')) }}",
      "text": "{{ e(app()->getLocale()==='en' ? ($step['description_en']??$step['description_fr']??'') : ($step['description_fr']??$step['description_en']??'')) }}"
    }{{ !$loop->last ? ',' : '' }}
    @endforeach
  ]
  @endif
}
</script>
@endsection

@section('content')
<div class="help-layout">
    {{-- SIDEBAR --}}
    <aside class="help-sidebar">
        <p class="sidebar-title">{{ app()->getLocale()==='en' ? 'Categories' : 'Catégories' }}</p>
        <a href="{{ url('help') }}" class="sidebar-cat">
            <span class="cat-icon">🏠</span>
            {{ app()->getLocale()==='en' ? 'All topics' : 'Tous les sujets' }}
        </a>
        @foreach($allCategories as $cat)
            <a href="{{ url('help/'.$cat->slug) }}" class="sidebar-cat {{ $cat->id === $article->help_category_id ? 'active' : '' }}">
                <span class="cat-icon">{{ $cat->icon }}</span>
                {{ $cat->trans('name') }}
            </a>
        @endforeach
    </aside>

    {{-- MAIN --}}
    <main class="help-main">
        <article class="article-wrap">
            {{-- Breadcrumb --}}
            <nav style="font-size:13px;color:var(--gray);margin-bottom:20px;">
                <a href="{{ url('help') }}" style="color:var(--sky);text-decoration:none;">Help Center</a>
                <span style="margin:0 6px;">›</span>
                <a href="{{ url('help/'.$article->category->slug) }}" style="color:var(--sky);text-decoration:none;">{{ $article->category->trans('name') }}</a>
                <span style="margin:0 6px;">›</span>
                <span>{{ $article->trans('title') }}</span>
            </nav>

            {{-- Header --}}
            <div class="article-header">
                <div class="article-meta-row">
                    <span class="badge-type badge-{{ $article->type }}">{{ $article->type_icon }} {{ $article->type_label }}</span>
                    @if($article->difficulty)
                        <span class="badge-difficulty" style="color:{{ $article->difficulty_color }};">{{ $article->difficulty_label }}</span>
                    @endif
                    <span class="sep">·</span>
                    <span>⏱ {{ $article->reading_time }} min</span>
                    @if($article->published_at)
                        <span class="sep">·</span>
                        <span>{{ $article->published_at->translatedFormat('d M Y') }}</span>
                    @endif
                    <span class="sep">·</span>
                    <span>👁 {{ number_format($article->views) }} {{ app()->getLocale()==='en'?'views':'vues' }}</span>
                </div>
                <h1>{{ $article->trans('title') }}</h1>
                @if($article->trans('excerpt'))
                    <p style="font-size:17px;color:var(--gray);line-height:1.7;margin-top:12px;">{{ $article->trans('excerpt') }}</p>
                @endif
            </div>

            {{-- Featured image --}}
            @if($article->featured_image)
                <img src="{{ asset('storage/'.$article->featured_image) }}" alt="{{ $article->trans('title') }}" class="featured-img">
            @endif

            {{-- GUIDE: interactive steps panel (shown BEFORE content for guides) --}}
            @if($article->type === 'guide' && $article->steps)
            <div class="guide-steps">
                <h2>
                    {{ app()->getLocale()==='en' ? '🗺️ Step-by-step guide' : '🗺️ Guide pas à pas' }}
                </h2>
                <div id="guide-progress" style="background:#f1f5f9;border-radius:8px;height:6px;margin-bottom:24px;overflow:hidden;">
                    <div id="guide-bar" style="height:100%;background:var(--sky);border-radius:8px;transition:width .4s;width:0%;"></div>
                </div>
                @foreach($article->steps as $i => $step)
                    @php
                        $stepTitle = app()->getLocale()==='en'
                            ? ($step['title_en'] ?? $step['title_fr'] ?? '')
                            : ($step['title_fr'] ?? $step['title_en'] ?? '');
                        $stepDesc = app()->getLocale()==='en'
                            ? ($step['description_en'] ?? $step['description_fr'] ?? '')
                            : ($step['description_fr'] ?? $step['description_en'] ?? '');
                    @endphp
                    <div class="step-item" id="step-{{ $i }}" data-step="{{ $i }}">
                        <div class="step-num">{{ $i + 1 }}</div>
                        <div class="step-content">
                            <h3>{{ $step['icon'] ?? '✅' }} {{ $stepTitle }}</h3>
                            @if($stepDesc)<p>{{ $stepDesc }}</p>@endif
                        </div>
                        <div style="margin-left:auto;flex-shrink:0;">
                            <button onclick="markStep({{ $i }},{{ count($article->steps) }})"
                                id="btn-step-{{ $i }}"
                                style="padding:6px 14px;border-radius:8px;border:1.5px solid var(--sky);background:#fff;color:var(--sky);font-size:13px;font-weight:600;cursor:pointer;">
                                {{ app()->getLocale()==='en' ? 'Done' : 'Fait' }}
                            </button>
                        </div>
                    </div>
                @endforeach
                <div id="guide-complete" style="display:none;text-align:center;padding:24px;background:#dcfce7;border-radius:14px;margin-top:16px;">
                    <div style="font-size:36px;margin-bottom:8px;">🎉</div>
                    <p style="font-weight:700;font-size:17px;color:#15803d;">
                        {{ app()->getLocale()==='en' ? 'Guide completed! Well done.' : 'Guide terminé ! Bravo.' }}
                    </p>
                </div>
            </div>
            @endif

            {{-- Main content --}}
            <div class="article-body">
                {!! $article->trans('content') !!}
            </div>

            {{-- TUTORIAL: steps shown AFTER content --}}
            @if($article->type === 'tutorial' && $article->steps)
            <div class="guide-steps" style="margin-top:40px;">
                <h2>{{ app()->getLocale()==='en' ? '🎓 Step-by-step walkthrough' : '🎓 Étapes détaillées' }}</h2>
                @foreach($article->steps as $i => $step)
                    @php
                        $stepTitle = app()->getLocale()==='en'
                            ? ($step['title_en'] ?? $step['title_fr'] ?? '')
                            : ($step['title_fr'] ?? $step['title_en'] ?? '');
                        $stepDesc = app()->getLocale()==='en'
                            ? ($step['description_en'] ?? $step['description_fr'] ?? '')
                            : ($step['description_fr'] ?? $step['description_en'] ?? '');
                    @endphp
                    <div class="step-item">
                        <div class="step-num">{{ $i + 1 }}</div>
                        <div class="step-content">
                            <h3>{{ $step['icon'] ?? '▶️' }} {{ $stepTitle }}</h3>
                            @if($stepDesc)<p>{{ $stepDesc }}</p>@endif
                        </div>
                    </div>
                @endforeach
            </div>
            @endif

            {{-- Author / feedback --}}
            <div style="margin-top:40px;padding:20px 24px;background:var(--light);border-radius:14px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
                <div>
                    <p style="font-size:14px;font-weight:600;">{{ app()->getLocale()==='en' ? 'Written by' : 'Rédigé par' }} {{ $article->author_name }}</p>
                    <p style="font-size:13px;color:var(--gray);">
                        {{ app()->getLocale()==='en' ? 'Last updated:' : 'Dernière mise à jour :' }}
                        {{ $article->updated_at->format('d/m/Y') }}
                    </p>
                </div>
                <div style="display:flex;gap:10px;align-items:center;">
                    <span style="font-size:14px;color:var(--gray);">{{ app()->getLocale()==='en' ? 'Was this helpful?' : 'Cet article vous a aidé ?' }}</span>
                    <button onclick="this.textContent='✅';this.style.background='#dcfce7';this.style.borderColor='#22c55e';" style="padding:6px 14px;border-radius:8px;border:1.5px solid #e2e8f0;background:#fff;font-size:14px;cursor:pointer;">👍 {{ app()->getLocale()==='en'?'Yes':'Oui' }}</button>
                    <button onclick="this.textContent='✅';this.style.background='#fee2e2';this.style.borderColor='#ef4444';" style="padding:6px 14px;border-radius:8px;border:1.5px solid #e2e8f0;background:#fff;font-size:14px;cursor:pointer;">👎 {{ app()->getLocale()==='en'?'No':'Non' }}</button>
                </div>
            </div>

            {{-- Related articles --}}
            @if($related->count())
            <div class="related-section">
                <h2>{{ app()->getLocale()==='en' ? '🔗 Related articles' : '🔗 Articles connexes' }}</h2>
                <div class="related-grid">
                    @foreach($related as $rel)
                        <a href="{{ url('help/article/'.$rel->slug) }}" class="related-card">
                            {{ $rel->type_icon }} {{ $rel->trans('title') }}
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
        </article>
    </main>
</div>

<script>
const totalSteps = {{ $article->steps ? count($article->steps) : 0 }};
const done = new Set();
function markStep(i, total) {
    done.add(i);
    const btn = document.getElementById('btn-step-' + i);
    const item = document.getElementById('step-' + i);
    btn.textContent = '✓';
    btn.style.background = 'var(--sky)';
    btn.style.color = '#fff';
    btn.disabled = true;
    item.style.opacity = '.7';
    const pct = Math.round((done.size / total) * 100);
    document.getElementById('guide-bar').style.width = pct + '%';
    if (done.size === total) {
        document.getElementById('guide-complete').style.display = 'block';
    }
}
</script>
@endsection
