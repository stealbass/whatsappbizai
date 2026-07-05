<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'business_id', 'plan', 'status', 'billing_cycle',
        'starts_at', 'ends_at',
        'flutterwave_tx_ref', 'flutterwave_tx_id',
        'amount_paid', 'currency', 'features',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
        'features'  => 'array',
    ];

    // Limits par plan
    public static array $plans = [
        'free'     => ['contacts' => 50,   'invoices' => 10,  'messages' => 100,  'price_xaf_monthly' => 0,     'price_xaf_yearly' => 0],
        'starter'  => ['contacts' => 500,  'invoices' => 100, 'messages' => 1000, 'price_xaf_monthly' => 9900,  'price_xaf_yearly' => 99000],
        'business' => ['contacts' => 2000, 'invoices' => 500, 'messages' => 5000, 'price_xaf_monthly' => 24900, 'price_xaf_yearly' => 249000],
        'pro'      => ['contacts' => -1,   'invoices' => -1,  'messages' => -1,   'price_xaf_monthly' => 49900, 'price_xaf_yearly' => 499000],
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && ($this->ends_at === null || $this->ends_at->isFuture());
    }

    public function daysLeft(): int
    {
        if (!$this->ends_at) return 9999;
        return max(0, now()->diffInDays($this->ends_at, false));
    }
}
