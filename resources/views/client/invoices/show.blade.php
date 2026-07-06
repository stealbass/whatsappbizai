@extends('client.layout')
@section('title', __('app.client.invoices.title') . ' ' . $invoice->number)

@section('content')
<div class="card" style="max-width:800px;">
    <div class="card-header">
        <h2>{{ __('app.client.invoices.title') }} {{ $invoice->number }}</h2>
        <span class="status {{ $invoice->status }}">
                    @if($invoice->status === 'paid'){{ __('app.client.invoices.status_paid') }}
                    @elseif($invoice->status === 'pending'){{ __('app.client.invoices.status_pending') }}
                    @elseif($invoice->status === 'overdue'){{ __('app.client.invoices.status_overdue') }}
                    @elseif($invoice->status === 'draft'){{ __('app.client.invoices.status_draft') }}
                    @else{{ ucfirst($invoice->status) }}@endif
                </span>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
        <div>
            <p style="font-size:12px;color:var(--gray);margin-bottom:4px;">{{ __('app.client.invoices.client') }}</p>
            <p style="font-weight:600;">{{ $invoice->contact->name ?? '-' }}</p>
            <p style="font-size:13px;color:var(--gray);">{{ $invoice->contact->email ?? '' }}</p>
            <p style="font-size:13px;color:var(--gray);">{{ $invoice->contact->whatsapp_number ?? '' }}</p>
        </div>
        <div style="text-align:right;">
            <p style="font-size:12px;color:var(--gray);margin-bottom:4px;">{{ __('app.client.invoices.form.issue_date') }}</p>
            <p style="font-weight:600;">{{ \Carbon\Carbon::parse($invoice->issue_date)->format('d/m/Y') }}</p>
            <p style="font-size:12px;color:var(--gray);margin-bottom:4px;margin-top:8px;">{{ __('app.client.invoices.due_date') }}</p>
            <p style="font-weight:600;">{{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') : '-' }}</p>
        </div>
    </div>

    @if($invoice->notes)
        <div style="margin-bottom:20px;">
            <p style="font-size:12px;color:var(--gray);margin-bottom:4px;">{{ __('app.client.invoices.notes') }}</p>
            <p style="font-size:14px;">{!! nl2br(e($invoice->notes)) !!}</p>
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>{{ __('app.client.invoices.form.description') }}</th>
                <th style="text-align:center;">{{ __('app.client.invoices.th_qty') }}</th>
                <th style="text-align:right;">{{ __('app.client.invoices.form.unit_price') }}</th>
                <th style="text-align:right;">{{ __('app.client.invoices.total') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td style="text-align:center;">{{ $item->quantity }}</td>
                <td style="text-align:right;">{{ number_format($item->unit_price, 0, ',', ' ') }}</td>
                <td style="text-align:right;font-weight:600;">{{ number_format($item->total, 0, ',', ' ') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align:right;font-weight:600;">{{ __('app.client.invoices.subtotal') }}</td>
                <td style="text-align:right;">{{ number_format($invoice->subtotal, 0, ',', ' ') }} {{ $invoice->currency }}</td>
            </tr>
            @if($invoice->tax_rate > 0)
            <tr>
                <td colspan="3" style="text-align:right;">{{ __('app.client.invoices.tax') }} ({{ $invoice->tax_rate }}%)</td>
                <td style="text-align:right;">{{ number_format($invoice->tax_amount, 0, ',', ' ') }} {{ $invoice->currency }}</td>
            </tr>
            @endif
            @if($invoice->discount > 0)
            <tr>
                <td colspan="3" style="text-align:right;color:var(--red);">{{ __('app.client.invoices.discount') }}</td>
                <td style="text-align:right;color:var(--red);">-{{ number_format($invoice->discount, 0, ',', ' ') }} {{ $invoice->currency }}</td>
            </tr>
            @endif
            <tr>
                <td colspan="3" style="text-align:right;font-weight:700;border-top:2px solid var(--border);">{{ __('app.client.invoices.total') }}</td>
                <td style="text-align:right;font-weight:700;font-size:16px;border-top:2px solid var(--border);">{{ number_format($invoice->total, 0, ',', ' ') }} {{ $invoice->currency }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="display:flex;flex-wrap:wrap;gap:10px;margin-top:24px;">
        <a href="{{ url('client/invoices') }}" class="btn btn-outline">{{ __('app.client.invoices.back') }}</a>

        @if(!in_array($invoice->status, ['paid', 'cancelled']))
            <form action="{{ url('client/invoices/' . $invoice->id . '/mark-paid') }}" method="POST" onsubmit="return confirm('{{ __('app.client.invoices.confirm_paid') }}')">
                @csrf
                <button type="submit" class="btn btn-primary">✅ {{ __('app.client.invoices.mark_paid') }}</button>
            </form>
        @endif

        @if($invoice->contact && $invoice->contact->whatsapp_number && !in_array($invoice->status, ['paid']))
            @if($business->whatsapp_phone_number_id)
                <form action="{{ url('client/invoices/' . $invoice->id . '/reminder') }}" method="POST" onsubmit="return confirm('{{ __('app.client.invoices.confirm_reminder') }}')">
                    @csrf
                    <button type="submit" class="btn btn-outline">🔔 {{ __('app.client.invoices.reminder') }}</button>
                </form>
                <form action="{{ url('client/invoices/' . $invoice->id . '/whatsapp') }}" method="POST" onsubmit="return confirm('{{ __('app.client.invoices.confirm_send') }}')">
                    @csrf
                    <button type="submit" class="btn btn-outline">📲 {{ __('app.client.invoices.send_whatsapp') }}</button>
                </form>
            @endif
        @endif

        <a href="{{ url('client/invoices/' . $invoice->id . '/pdf') }}" class="btn btn-outline" target="_blank">📥 {{ __('app.client.invoices.download_pdf') }}</a>

        @if(!in_array($invoice->status, ['paid', 'cancelled']))
            <form action="{{ url('client/invoices/' . $invoice->id) }}" method="POST" style="margin-left:auto;" onsubmit="return confirm('{{ __('app.client.invoices.confirm_delete') }}')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">{{ __('app.client.invoices.delete') }}</button>
            </form>
        @endif
    </div>
</div>
@endsection
