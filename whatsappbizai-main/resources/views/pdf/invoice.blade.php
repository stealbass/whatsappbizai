<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1a1a1a; background: #fff; }
    .header { display: flex; justify-content: space-between; padding: 30px; background: #0f172a; color: #fff; }
    .header .brand { font-size: 22px; font-weight: bold; }
    .header .invoice-label { font-size: 28px; font-weight: bold; color: #38bdf8; text-align: right; }
    .header .invoice-meta { font-size: 11px; color: #94a3b8; text-align: right; margin-top: 4px; }
    .addresses { display: flex; justify-content: space-between; padding: 24px 30px; }
    .address-block h4 { font-size: 10px; text-transform: uppercase; color: #64748b; margin-bottom: 6px; letter-spacing: .05em; }
    .address-block p { font-size: 12px; line-height: 1.6; }
    table.items { width: 100%; border-collapse: collapse; margin: 0 30px; width: calc(100% - 60px); }
    table.items thead tr { background: #0f172a; color: #fff; }
    table.items thead th { padding: 10px 12px; text-align: left; font-size: 11px; font-weight: 600; }
    table.items thead th.right { text-align: right; }
    table.items tbody tr:nth-child(even) { background: #f8fafc; }
    table.items tbody td { padding: 10px 12px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
    table.items tbody td.right { text-align: right; }
    .totals { margin: 0 30px; margin-top: 16px; }
    .totals table { width: 280px; margin-left: auto; }
    .totals table td { padding: 6px 10px; font-size: 12px; }
    .totals table td:last-child { text-align: right; }
    .totals .total-row td { font-size: 14px; font-weight: bold; background: #0f172a; color: #fff; padding: 10px; border-radius: 4px; }
    .status-badge { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 10px; font-weight: 600; text-transform: uppercase; }
    .status-draft   { background: #e2e8f0; color: #475569; }
    .status-sent    { background: #fef3c7; color: #92400e; }
    .status-paid    { background: #d1fae5; color: #065f46; }
    .status-overdue { background: #fee2e2; color: #991b1b; }
    .notes { margin: 20px 30px 0; padding: 14px; background: #f8fafc; border-left: 3px solid #38bdf8; font-size: 11px; color: #475569; }
    .footer { margin-top: 40px; padding: 16px 30px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 10px; color: #94a3b8; }
</style>
</head>
<body>

<div class="header">
    <div>
        <div class="brand">{{ $invoice->business->name }}</div>
        <div style="font-size:11px; color:#94a3b8; margin-top:6px;">
            {{ $invoice->business->address }}<br>
            {{ $invoice->business->city }}, {{ $invoice->business->country }}<br>
            {{ $invoice->business->email }}
        </div>
    </div>
    <div>
        <div class="invoice-label">{{ __('app.pdf.invoice.title') }}</div>
        <div class="invoice-meta">
            N° {{ $invoice->number }}<br>
            {{ __('app.pdf.invoice.issued_on') }} {{ $invoice->issue_date->format('d/m/Y') }}<br>
            {{ __('app.pdf.invoice.due_date') }} {{ $invoice->due_date->format('d/m/Y') }}
        </div>
    </div>
</div>

<div class="addresses">
    <div class="address-block">
        <h4>{{ __('app.pdf.invoice.billed_to') }}</h4>
        <p>
            <strong>{{ $invoice->contact->name }}</strong><br>
            @if($invoice->contact->company) {{ $invoice->contact->company }}<br> @endif
            @if($invoice->contact->email) {{ $invoice->contact->email }}<br> @endif
            {{ $invoice->contact->whatsapp_number }}
        </p>
    </div>
    <div class="address-block" style="text-align:right;">
        <h4>{{ __('app.pdf.invoice.status') }}</h4>
        <span class="status-badge status-{{ $invoice->status }}">
            @switch($invoice->status)
                @case('draft') {{ __('app.pdf.invoice.status_draft') }} @break
                @case('sent') {{ __('app.pdf.invoice.status_sent') }} @break
                @case('paid') {{ __('app.pdf.invoice.status_paid') }} @break
                @case('overdue') {{ __('app.pdf.invoice.status_overdue') }} @break
                @default {{ $invoice->status }}
            @endswitch
        </span>
    </div>
</div>

<table class="items">
    <thead>
        <tr>
            <th style="width:50%">{{ __('app.pdf.invoice.description') }}</th>
            <th class="right" style="width:12%">{{ __('app.pdf.invoice.qty') }}</th>
            <th class="right" style="width:18%">{{ __('app.pdf.invoice.unit_price') }}</th>
            <th class="right" style="width:20%">{{ __('app.pdf.invoice.total') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoice->items as $item)
        <tr>
            <td>{{ $item->description }}</td>
            <td class="right">{{ number_format($item->quantity, 0) }}</td>
            <td class="right">{{ number_format($item->unit_price, 0, ',', ' ') }} {{ $invoice->currency }}</td>
            <td class="right">{{ number_format($item->total, 0, ',', ' ') }} {{ $invoice->currency }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="totals">
    <table>
        <tr>
            <td>{{ __('app.pdf.invoice.subtotal') }}</td>
            <td>{{ number_format($invoice->subtotal, 0, ',', ' ') }} {{ $invoice->currency }}</td>
        </tr>
        @if($invoice->discount > 0)
        <tr>
            <td>{{ __('app.pdf.invoice.discount') }}</td>
            <td>- {{ number_format($invoice->discount, 0, ',', ' ') }} {{ $invoice->currency }}</td>
        </tr>
        @endif
        @if($invoice->tax_rate > 0)
        <tr>
            <td>{{ __('app.pdf.invoice.tax') }} ({{ $invoice->tax_rate }}%)</td>
            <td>{{ number_format($invoice->tax_amount, 0, ',', ' ') }} {{ $invoice->currency }}</td>
        </tr>
        @endif
        <tr class="total-row">
            <td>{{ __('app.pdf.invoice.grand_total') }}</td>
            <td>{{ number_format($invoice->total, 0, ',', ' ') }} {{ $invoice->currency }}</td>
        </tr>
        @if($invoice->paid_amount > 0)
        <tr>
            <td style="color:#065f46;">{{ __('app.pdf.invoice.amount_paid') }}</td>
            <td style="color:#065f46;">{{ number_format($invoice->paid_amount, 0, ',', ' ') }} {{ $invoice->currency }}</td>
        </tr>
        <tr>
            <td style="font-weight:bold;">{{ __('app.pdf.invoice.balance') }}</td>
            <td style="font-weight:bold;">{{ number_format($invoice->balance, 0, ',', ' ') }} {{ $invoice->currency }}</td>
        </tr>
        @endif
    </table>
</div>

@if($invoice->notes)
<div class="notes">
    <strong>{{ __('app.pdf.invoice.notes') }}</strong> {!! strip_tags($invoice->notes, '<b><strong><em><u><ul><ol><li><br><p>') !!}
</div>
@endif

<div class="footer">
    {{ $invoice->business->name }} — {{ $invoice->business->email }}
    @if($invoice->business->phone) · {{ $invoice->business->phone }} @endif
    · {{ __('app.pdf.invoice.generated_by') }}
</div>

</body>
</html>
