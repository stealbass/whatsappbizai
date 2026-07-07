<?php

namespace App\Models;

use App\Models\Scopes\BusinessScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::addGlobalScope(new BusinessScope);
    }

    protected $fillable = [
        'business_id',
        'name',
        'description',
        'unit_price',
        'currency',
        'unit',         // heure | jour | forfait | unité
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'is_active'  => 'boolean',
        ];
    }

    public function business() { return $this->belongsTo(Business::class); }
}
