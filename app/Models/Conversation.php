<?php

namespace App\Models;

use App\Models\Scopes\BusinessScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::addGlobalScope(new BusinessScope);
    }

    protected $fillable = [
        'business_id',
        'contact_id',
        'whatsapp_thread_id',
        'status',           // open | closed | waiting
        'channel',          // whatsapp
        'ai_enabled',
        'summary',          // résumé IA de la conversation
        'last_message_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'ai_enabled'      => 'boolean',
            'last_message_at' => 'datetime',
            'closed_at'       => 'datetime',
        ];
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    /**
     * Retourne les N derniers messages pour le contexte Gemini
     */
    public function contextMessages(int $limit = 20): array
    {
        return $this->messages()
            ->latest()
            ->limit($limit)
            ->get()
            ->reverse()
            ->map(fn($m) => [
                'role'    => $m->direction === 'inbound' ? 'user' : 'model',
                'content' => $m->content,
            ])
            ->values()
            ->toArray();
    }
}
