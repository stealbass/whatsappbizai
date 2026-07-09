<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class HelpCategory extends Model
{
    protected $fillable = [
        'slug', 'icon', 'color',
        'name_fr', 'name_en',
        'description_fr', 'description_en',
        'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function articles()
    {
        return $this->hasMany(HelpArticle::class);
    }

    public function publishedArticles()
    {
        return $this->hasMany(HelpArticle::class)->where('is_published', true);
    }

    public function trans(string $field): ?string
    {
        $locale = App::getLocale();
        return match ($locale) {
            'en' => $this->{"${field}_en"} ?? $this->{"${field}_fr"},
            default => $this->{"${field}_fr"} ?? $this->{"${field}_en"},
        };
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
