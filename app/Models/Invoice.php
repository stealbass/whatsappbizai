<?php

namespace App\Models;

use App\Models\Scopes\BusinessScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::addGlobalScope(new BusinessScope);
    }

    protected $fillable = [
        'business_id',
        'contact_id',
        'number',           // ex: FAC-2026-0001
        'status',           // draft | sent | paid | overdue | cancelled
        'issue_date',
        'due_date',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'discount',
        'total',
        'paid_amount',
        'currency',
        'notes',
        'payment_method',
        'paid_at',
        'sent_at',
        'pdf_path',
        'whatsapp_sent',
    ];

    protected function casts(): array
    {
        return [
            'issue_date'     => 'date',
            'due_date'       => 'date',
            'paid_at'        => 'datetime',
            'sent_at'        => 'datetime',
            'subtotal'       => 'decimal:2',
            'tax_amount'     => 'decimal:2',
            'discount'       => 'decimal:2',
            'total'          => 'decimal:2',
            'paid_amount'    => 'decimal:2',
            'tax_rate'       => 'decimal:2',
            'whatsapp_sent'  => 'boolean',
        ];
    }

    public function business()  { return $this->belongsTo(Business::class); }
    public function contact()   { return $this->belongsTo(Contact::class); }
    public function items()     { return $this->hasMany(InvoiceItem::class); }

    public function getBalanceAttribute(): float
    {
        return (float) $this->total - (float) $this->paid_amount;
    }

    public function isOverdue(): bool
    {
        return $this->due_date < now() && $this->status !== 'paid';
    }
}
