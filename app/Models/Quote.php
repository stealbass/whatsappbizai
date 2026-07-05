<?php

namespace App\Models;

use App\Models\Scopes\BusinessScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::addGlobalScope(new BusinessScope);
    }

    protected $fillable = [
        'business_id',
        'contact_id',
        'number',           // ex: DEV-2026-0001
        'status',           // draft | sent | accepted | declined | expired
        'issue_date',
        'valid_until',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'discount',
        'total',
        'currency',
        'notes',
        'pdf_path',
        'whatsapp_sent',
        'converted_to_invoice_id',
    ];

    protected function casts(): array
    {
        return [
            'issue_date'              => 'date',
            'valid_until'             => 'date',
            'subtotal'                => 'decimal:2',
            'tax_amount'              => 'decimal:2',
            'discount'                => 'decimal:2',
            'total'                   => 'decimal:2',
            'tax_rate'                => 'decimal:2',
            'whatsapp_sent'           => 'boolean',
        ];
    }

    public function business()   { return $this->belongsTo(Business::class); }
    public function contact()    { return $this->belongsTo(Contact::class); }
    public function items()      { return $this->hasMany(QuoteItem::class); }
    public function invoice()    { return $this->belongsTo(Invoice::class, 'converted_to_invoice_id'); }

    public function isExpired(): bool
    {
        return $this->valid_until < now() && $this->status !== 'accepted';
    }
}
