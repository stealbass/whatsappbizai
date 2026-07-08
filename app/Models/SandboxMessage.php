<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SandboxMessage extends Model
{
    protected $fillable = [
        'business_id',
        'to',
        'contact_name',
        'type',
        'content',
        'media_url',
        'trigger',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
