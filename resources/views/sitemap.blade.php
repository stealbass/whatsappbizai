<?php
use App\Models\Post;

$posts = Post::published()->pluck('slug', 'updated_at');
?>
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>http://localhost/testwebsite/whatsappbizai-main/public/</loc>
        <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>http://localhost/testwebsite/whatsappbizai-main/public/pricing</loc>
        <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>http://localhost/testwebsite/whatsappbizai-main/public/blog</loc>
        <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @foreach($posts as $slug => $updatedAt)
    <url>
        <loc>{{ url('blog/' . $slug) }}</loc>
        <lastmod>{{ $updatedAt instanceof \Carbon\Carbon ? $updatedAt->format('Y-m-d') : date('Y-m-d') }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach
    <url>
        <loc>http://localhost/testwebsite/whatsappbizai-main/public/privacy</loc>
        <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
        <changefreq>yearly</changefreq>
        <priority>0.3</priority>
    </url>
    <url>
        <loc>http://localhost/testwebsite/whatsappbizai-main/public/terms</loc>
        <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
        <changefreq>yearly</changefreq>
        <priority>0.3</priority>
    </url>
    <url>
        <loc>http://localhost/testwebsite/whatsappbizai-main/public/contact</loc>
        <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    <url>
        <loc>http://localhost/testwebsite/whatsappbizai-main/public/register</loc>
        <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
        <changefreq>yearly</changefreq>
        <priority>0.5</priority>
    </url>
    <url>
        <loc>http://localhost/testwebsite/whatsappbizai-main/public/login</loc>
        <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
        <changefreq>yearly</changefreq>
        <priority>0.5</priority>
    </url>
</urlset>
