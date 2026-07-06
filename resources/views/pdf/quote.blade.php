<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1a1a1a; }
    .header { display: flex; justify-content: space-between; padding: 30px; background: #064e3b; color: #fff; }
    .header .brand { font-size: 22px; font-weight: bold; }
    .header .quote-label { font-size: 28px; font-weight: bold; color: #6ee7b7; text-align: right; }
    .header .quote-meta { font-size: 11px; color: #a7f3d0; text-align: right; margin-top: 4px; }
    .addresses { display: flex; justify-content: space-between; padding: 24px 30px; }
    .address-block h4 { font-size: 10px; text-transform: uppercase; color: #64748b; margin-bottom: 6px; }
    table.items { width: calc(100% - 60px); margin: 0 30px; border-collapse: collapse; }
    table.items thead tr { background: #064e3b; color: #fff; }
    table.items thead th { padding: 10px 12px; text-align: left; font-size: 11px; }
    table.items thead th.right { text-align: right; }
    table.items tbody tr:nth-child(even) { background: #f0fdf4; }
    table.items tbody td { padding: 10px 12px; border-bottom: 1px solid #d1fae5; }
    table.items tbody td.right { text-align: right; }
    .totals { margin: 16px 30px 0; }
    .totals table { width: 280px; margin-left: auto; }
    .totals table td { padding: 6px 10px; }
    .totals table td:last-child { text-align: right; }
    .totals .total-row td { font-size: 14px; font-weight: bold; background: #064e3b; color: #fff; padding: 10px; }
    .validity { margin: 20px 30px 0; padding: 12px 16px; background: #fffbeb; border-left: 3px solid #f59e0b; font-size: 11px; }
    .notes { margin: 12px 30px 0; padding: 12px 16px; background: #f8fafc; border-left: 3px solid #6ee7b7; font-size: 11px; color: #475569; }
    .footer { margin-top: 40px; padding: 16px 30px; border-top: 1px solid #d1fae5; text-align: center; font-size: 10px; color: #94a3b8; }
</style>
</head>
<body>

<div class="header">
    <div>
        <div class="brand">{{ $quote->business->name }}</div>
        <div style="font-size:11px;color:#a7f3d0;margin-top:6px;">
            {{ $quote->business->address }}<br>
            {{ $quote->business->city }}, {{ $quote->business->country }}<br>
            {{ $quote->business->email }}
        </div>
    </div>
    <div>
        <div class="quote-label">{{ __('app.pdf.quote.title') }}</div>
        <div class="quote-meta">
            N° {{ $quote->number }}<br>
            {{ __('app.pdf.quote.issued_on') }} {{ $quote->issue_date->format('d/m/Y') }}<br>
            {{ __('app.pdf.quote.valid_until') }} {{ $quote->valid_until->format('d/m/Y') }}
        </div>
    </div>
</div>

<div class="addresses">
    <div class="address-block">
        <h4>{{ __('app.pdf.quote.proposed_to') }}</h4>
        <p>
            <strong>{{ $quote->contact->name }}</strong><br>
            @if($quote->contact->company) {{ $quote->contact->company }}<br> @endif
            @if($quote->contact->email) {{ $quote->contact->email }}<br> @endif
            {{ $quote->contact->whatsapp_number }}
        </p>
    </div>
</div>

<table class="items">
    <thead>
        <tr>
            <th style="width:50%">{{ __('app.pdf.quote.description') }}</th>
            <th class="right" style="width:12%">{{ __('app.pdf.quote.qty') }}</th>
            <th class="right" style="width:18%">{{ __('app.pdf.quote.unit_price') }}</th>
            <th class="right" style="width:20%">{{ __('app.pdf.quote.total') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($quote->items as $item)
        <tr>
            <td>{{ $item->description }}</td>
            <td class="right">{{ number_format($item->quantity, 0) }}</td>
            <td class="right">{{ number_format($item->unit_price, 0, ',', ' ') }} {{ $quote->currency }}</td>
            <td class="right">{{ number_format($item->total, 0, ',', ' ') }} {{ $quote->currency }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="totals">
    <table>
        <tr><td>{{ __('app.pdf.quote.subtotal') }}</td><td>{{ number_format($quote->subtotal, 0, ',', ' ') }} {{ $quote->currency }}</td></tr>
        @if($quote->discount > 0)
        <tr><td>{{ __('app.pdf.quote.discount') }}</td><td>- {{ number_format($quote->discount, 0, ',', ' ') }} {{ $quote->currency }}</td></tr>
        @endif
        @if($quote->tax_rate > 0)
        <tr><td>{{ __('app.pdf.quote.tax') }} ({{ $quote->tax_rate }}%)</td><td>{{ number_format($quote->tax_amount, 0, ',', ' ') }} {{ $quote->currency }}</td></tr>
        @endif
        <tr class="total-row">
            <td>{{ __('app.pdf.quote.grand_total') }}</td>
            <td>{{ number_format($quote->total, 0, ',', ' ') }} {{ $quote->currency }}</td>
        </tr>
    </table>
</div>

<div class="validity">
    ⏳ {{ __('app.pdf.quote.validity_text') }} <strong>{{ $quote->valid_until->format('d/m/Y') }}</strong>.
    Pour l'accepter, répondez simplement <strong>"OK"</strong> ou <strong>"Accepté"</strong> sur WhatsApp.
</div>

@if($quote->notes)
<div class="notes"><strong>{{ __('app.pdf.quote.conditions') }}</strong> {{ $quote->notes }}</div>
@endif

<div class="footer">
    {{ $quote->business->name }} — {{ $quote->business->email }}
    @if($quote->business->phone) · {{ $quote->business->phone }} @endif
    · {{ __('app.pdf.quote.generated_by') }}
</div>
</body>
</html>
