<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Plan extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'description',
        'price_monthly',
        'price_yearly',
        'currency',
        'max_contacts',
        'max_invoices',
        'max_messages',
        'features',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'price_monthly' => 'decimal:2',
        'price_yearly'  => 'decimal:2',
        'features'      => 'array',
        'is_active'     => 'boolean',
        'is_featured'   => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('price_monthly');
    }

    public function businesses()
    {
        return $this->hasMany(Business::class, 'plan', 'slug');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'plan', 'slug');
    }

    public function getDisplayPriceAttribute(): string
    {
        if ($this->price_monthly == 0) {
            return 'Gratuit';
        }

        return number_format($this->price_monthly, 0, ',', ' ') . ' ' . $this->currency . '/mois';
    }
}
