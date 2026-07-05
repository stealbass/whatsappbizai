<?php

namespace App\Models;

use App\Models\Scopes\BusinessScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Contact extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::addGlobalScope(new BusinessScope);

        // Génère automatiquement un token unique pour le portail client
        static::creating(function (self $contact) {
            if (empty($contact->portal_token)) {
                $contact->portal_token = Str::random(48);
            }
        });
    }

    protected $fillable = [
        'business_id',
        'whatsapp_number',
        'name',
        'email',
        'company',
        'notes',
        'tags',
        'status',       // prospect | client | inactif
        'last_seen_at',
        'total_invoiced',
        'total_paid',
        'portal_token',
    ];

    protected function casts(): array
    {
        return [
            'tags'          => 'array',
            'last_seen_at'  => 'datetime',
            'total_invoiced'=> 'decimal:2',
            'total_paid'    => 'decimal:2',
        ];
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

    /**
     * Conversation active (la plus récente ouverte)
     */
    public function activeConversation()
    {
        return $this->hasOne(Conversation::class)->latestOfMany()->where('status', 'open');
    }
}
