@extends('blog.layout')

@section('meta_title', __('app.blog.meta_title'))
@section('meta_description', __('app.blog.meta_description'))

@section('content')
<div class="blog-hero">
    <h1><span>Blog</span> — WhatsAppBizAI</h1>
    <p>{{ __('app.blog.subtitle') }}</p>
</div>

<div class="blog-container">
    @if($posts->count())
        <div class="posts-grid">
            @foreach($posts as $post)
                <a href="{{ url('blog/' . $post->slug) }}" class="post-card">
                    @if($post->featured_image)
                        <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="post-card-img">
                    @else
                        <div class="post-card-img" style="display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#e0f2fe,#f0f9ff);font-size:40px;">📝</div>
                    @endif
                    <div class="post-card-body">
                        @if($post->category)
                            <span class="post-card-category">{{ $post->category }}</span>
                        @endif
                        <h2 class="post-card-title">{{ $post->title }}</h2>
                        <p class="post-card-excerpt">{{ $post->excerpt ?? Str::limit(strip_tags($post->content), 120) }}</p>
                        <div class="post-card-meta">
                            <span>{{ $post->published_at?->format('d M Y') }}</span>
                            <span>{{ $post->reading_time }} min de lecture</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="pagination">
            {{ $posts->links() }}
        </div>
    @else
        <div style="text-align:center;padding:80px 24px;">
            <p style="font-size:18px;color:var(--gray);">{{ __('app.blog.no_posts') }}</p>
        </div>
    @endif
</div>
@endsection
