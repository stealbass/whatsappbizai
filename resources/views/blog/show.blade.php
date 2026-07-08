@extends('blog.layout')

@section('meta_title', $post->trans('meta_title') ?: $post->trans('title'))
@section('meta_description', $post->trans('meta_description') ?: $post->trans('excerpt'))

@section('content')
<script type="application/ld+json">
@php
$articleJson = [
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'headline' => $post->trans('title'),
    'description' => $post->trans('excerpt') ?: $post->trans('meta_description'),
    'url' => url('blog/' . $post->slug),
    'datePublished' => $post->published_at?->toIso8601String(),
    'dateModified' => $post->updated_at?->toIso8601String() ?? $post->published_at?->toIso8601String(),
    'author' => [
        '@type' => 'Organization',
        'name' => $post->author_name ?? 'WhatsAppBizAI',
        'url' => url('/'),
    ],
    'publisher' => [
        '@type' => 'Organization',
        'name' => $site->trans('site_name') ?? 'WhatsAppBizAI',
        'logo' => [
            '@type' => 'ImageObject',
            'url' => $site->logo_path ? asset('storage/' . $site->logo_path) : asset('logo.png'),
        ],
    ],
    'mainEntityOfPage' => [
        '@type' => 'WebPage',
        '@id' => url('blog/' . $post->slug),
    ],
    'inLanguage' => app()->getLocale(),
];
if ($post->featured_image) {
    $articleJson['image'] = asset('storage/' . $post->featured_image);
}
if ($post->category) {
    $articleJson['articleSection'] = $post->category;
}
@endphp
{!! json_encode($articleJson, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>

<div class="article-header">
    <a href="{{ url('blog') }}" class="back-link">← {{ __('app.blog.back_to_blog') }}</a>

    @if($post->category)
        <span class="category">{{ $post->category }}</span>
    @endif

    <h1>{{ $post->trans('title') }}</h1>

    <div class="article-meta">
        <span>{{ $post->author_name ?? 'WhatsAppBizAI' }}</span>
        <span>{{ $post->published_at?->format('d M Y') }}</span>
        <span>{{ $post->reading_time }} {{ __('app.blog.min_read') }}</span>
    </div>
</div>

<div class="article-content">
    @if($post->featured_image)
        <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="featured-img">
    @endif

    {!! $post->trans('content') !!}
</div>

@if($related->count())
<div class="related-section">
    <div class="related-inner">
        <h2>{{ __('app.blog.related_articles') }}</h2>
        <div class="posts-grid">
            @foreach($related as $r)
                <a href="{{ url('blog/' . $r->slug) }}" class="post-card">
                    @if($r->featured_image)
                        <img src="{{ asset('storage/' . $r->featured_image) }}" alt="{{ $r->title }}" class="post-card-img">
                    @else
                        <div class="post-card-img" style="display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#e0f2fe,#f0f9ff);font-size:40px;">📝</div>
                    @endif
                    <div class="post-card-body">
                        @if($r->category)
                            <span class="post-card-category">{{ $r->category }}</span>
                        @endif
                        <h2 class="post-card-title">{{ $r->title }}</h2>
                        <div class="post-card-meta">
                            <span>{{ $r->published_at?->format('d M Y') }}</span>
                            <span>{{ $r->reading_time }} min</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection
