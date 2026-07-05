<?php

namespace App\Models;

use App\Traits\BusinessScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory, BusinessScope;

    protected $fillable = [
        'conversation_id',
        'whatsapp_message_id',
        'direction',    // inbound | outbound
        'type',         // text | image | document | template | audio
        'content',
        'media_url',
        'media_mime',
        'status',       // sent | delivered | read | failed
        'is_ai',        // généré par Gemini ?
        'tokens_used',
        'error_message',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'is_ai'   => 'boolean',
            'sent_at' => 'datetime',
        ];
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}
