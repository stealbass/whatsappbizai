<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SandboxMessage;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'owner_name',
        'email',
        'phone',
        'whatsapp_phone_number_id',
        'whatsapp_access_token',
        'whatsapp_business_account_id',
        'gemini_system_prompt',
        'ai_documents',
        'address',
        'city',
        'country',
        'currency',       // XAF, EUR, USD...
        'logo_path',
        'invoice_prefix',
        'quote_prefix',
        'is_active',
        'sandbox_mode',
        'plan',           // free | starter | business | pro
        'plan_expires_at',
        'timezone',
    ];

    protected function casts(): array
    {
        return [
            'is_active'       => 'boolean',
            'sandbox_mode'    => 'boolean',
            'plan_expires_at' => 'datetime',
            'ai_documents'    => 'array',
        ];
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
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

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(\App\Models\Subscription::class);
    }

    public function payments()
    {
        return $this->hasMany(\App\Models\Payment::class);
    }

    public function sandboxMessages()
    {
        return $this->hasMany(SandboxMessage::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(\App\Models\Subscription::class)
            ->where('status', 'active')
            ->where(fn($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>', now()))
            ->latestOfMany();
    }
}
