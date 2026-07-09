<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class HelpArticle extends Model
{
    protected $fillable = [
        'help_category_id', 'slug', 'type',
        'title_fr', 'title_en',
        'excerpt_fr', 'excerpt_en',
        'content_fr', 'content_en',
        'meta_title_fr', 'meta_title_en',
        'meta_description_fr', 'meta_description_en',
        'featured_image', 'author_name',
        'is_published', 'published_at',
        'sort_order', 'views',
        'steps', 'difficulty', 'reading_minutes',
    ];

    protected $casts = [
        'is_published'  => 'boolean',
        'published_at'  => 'datetime',
        'steps'         => 'array',
    ];

    // ── Relations ──────────────────────────────────────────────────────────
    public function category()
    {
        return $this->belongsTo(HelpCategory::class, 'help_category_id');
    }

    // ── Translations ────────────────────────────────────────────────────────
    public function trans(string $field): ?string
    {
        $locale = App::getLocale();
        return match ($locale) {
            'en' => $this->{"${field}_en"} ?? $this->{"${field}_fr"},
            default => $this->{"${field}_fr"} ?? $this->{"${field}_en"},
        };
    }

    // ── Scopes ──────────────────────────────────────────────────────────────
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                     ->where('published_at', '<=', now());
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // ── Accessors ───────────────────────────────────────────────────────────
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getReadingTimeAttribute(): int
    {
        if ($this->reading_minutes) return $this->reading_minutes;
        $content = $this->trans('content') ?? '';
        $words   = str_word_count(strip_tags($content));
        return max(1, (int) ceil($words / 200));
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'tutorial' => '🎓',
            'guide'    => '🗺️',
            default    => '📄',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        $locale = App::getLocale();
        return match ($this->type) {
            'tutorial' => $locale === 'en' ? 'Tutorial'         : 'Tutoriel',
            'guide'    => $locale === 'en' ? 'Interactive Guide' : 'Guide interactif',
            default    => $locale === 'en' ? 'Article'           : 'Article',
        };
    }

    public function getDifficultyLabelAttribute(): string
    {
        $locale = App::getLocale();
        return match ($this->difficulty) {
            'beginner'     => $locale === 'en' ? 'Beginner'     : 'Débutant',
            'intermediate' => $locale === 'en' ? 'Intermediate' : 'Intermédiaire',
            'advanced'     => $locale === 'en' ? 'Advanced'     : 'Avancé',
            default        => '',
        };
    }

    public function getDifficultyColorAttribute(): string
    {
        return match ($this->difficulty) {
            'beginner'     => '#22c55e',
            'intermediate' => '#f59e0b',
            'advanced'     => '#ef4444',
            default        => '#64748b',
        };
    }
}
