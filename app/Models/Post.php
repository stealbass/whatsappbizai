<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'title_fr', 'title_en',
        'slug',
        'excerpt', 'excerpt_fr', 'excerpt_en',
        'content', 'content_fr', 'content_en',
        'featured_image',
        'category',
        'author_name',
        'is_published',
        'published_at',
        'meta_title', 'meta_title_fr', 'meta_title_en',
        'meta_description', 'meta_description_fr', 'meta_description_en',
        'sort_order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    const TRANSLATABLE = ['title', 'excerpt', 'content', 'meta_title', 'meta_description'];

    protected static function booted(): void
    {
        static::creating(function (Post $post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title_fr ?? $post->title);
            }
        });
    }

    /**
     * Get a translatable field for the current locale.
     */
    public function trans(string $field): ?string
    {
        $locale = App::getLocale();
        $fr = $this->{$field . '_fr'};
        $en = $this->{$field . '_en'};
        $base = $this->{$field};

        return match ($locale) {
            'en' => $en ?? $fr ?? $base,
            default => $fr ?? $en ?? $base,
        };
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                     ->where('published_at', '<=', now());
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getReadingTimeAttribute(): int
    {
        $content = $this->trans('content') ?? $this->content ?? '';
        $words = str_word_count(strip_tags($content));
        return max(1, (int) ceil($words / 200));
    }
}
