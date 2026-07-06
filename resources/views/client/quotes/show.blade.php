@extends('client.layout')
@section('title', __('app.client.quotes.title') . ' ' . $quote->number)

@section('content')
<div class="card" style="max-width:800px;">
    <div class="card-header">
        <h2>{{ __('app.client.quotes.title') }} {{ $quote->number }}</h2>
        <span class="status {{ $quote->status }}">{{ ucfirst($quote->status) }}</span>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
        <div>
            <p style="font-size:12px;color:var(--gray);margin-bottom:4px;">{{ __('app.client.quotes.client') }}</p>
            <p style="font-weight:600;">{{ $quote->contact->name ?? '-' }}</p>
            <p style="font-size:13px;color:var(--gray);">{{ $quote->contact->email ?? '' }}</p>
            <p style="font-size:13px;color:var(--gray);">{{ $quote->contact->whatsapp_number ?? '' }}</p>
        </div>
        <div style="text-align:right;">
            <p style="font-size:12px;color:var(--gray);margin-bottom:4px;">{{ __('app.client.quotes.valid_until') }}</p>
            <p style="font-weight:600;">{{ $quote->valid_until ? \Carbon\Carbon::parse($quote->valid_until)->format('d/m/Y') : '-' }}</p>
        </div>
    </div>

    @if($quote->notes)
        <div style="margin-bottom:20px;">
            <p style="font-size:12px;color:var(--gray);margin-bottom:4px;">{{ __('app.client.quotes.notes') }}</p>
            <div style="font-size:14px;line-height:1.6;">{!! $quote->notes !!}</div>
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>{{ __('app.client.quotes.form.description') }}</th>
                <th style="text-align:center;">{{ __('app.client.quotes.th_qty') }}</th>
                <th style="text-align:right;">{{ __('app.client.quotes.form.unit_price') }}</th>
                <th style="text-align:right;">{{ __('app.client.quotes.total') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quote->items as $item)
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
                <td colspan="3" style="text-align:right;font-weight:600;">{{ __('app.client.quotes.subtotal') }}</td>
                <td style="text-align:right;">{{ number_format($quote->subtotal, 0, ',', ' ') }} {{ $quote->currency }}</td>
            </tr>
            @if($quote->tax_rate > 0)
            <tr>
                <td colspan="3" style="text-align:right;">{{ __('app.client.quotes.tax') }} ({{ $quote->tax_rate }}%)</td>
                <td style="text-align:right;">{{ number_format($quote->tax_amount, 0, ',', ' ') }} {{ $quote->currency }}</td>
            </tr>
            @endif
            @if($quote->discount > 0)
            <tr>
                <td colspan="3" style="text-align:right;color:var(--red);">{{ __('app.client.quotes.discount') }}</td>
                <td style="text-align:right;color:var(--red);">-{{ number_format($quote->discount, 0, ',', ' ') }} {{ $quote->currency }}</td>
            </tr>
            @endif
            <tr>
                <td colspan="3" style="text-align:right;font-weight:700;border-top:2px solid var(--border);">{{ __('app.client.quotes.total') }}</td>
                <td style="text-align:right;font-weight:700;font-size:16px;border-top:2px solid var(--border);">{{ number_format($quote->total, 0, ',', ' ') }} {{ $quote->currency }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="display:flex;flex-wrap:wrap;gap:10px;margin-top:24px;">
        <a href="{{ url('client/quotes') }}" class="btn btn-outline">{{ __('app.client.quotes.back') }}</a>

        @if(in_array($quote->status, ['draft', 'sent']))
            <a href="{{ url('client/quotes/' . $quote->id . '/edit') }}" class="btn btn-outline">✏️ {{ __('app.client.common.edit') }}</a>
        @endif

        @if(!in_array($quote->status, ['accepted', 'declined']))
            @if($quote->contact && $quote->contact->whatsapp_number && $business->whatsapp_phone_number_id)
                <form action="{{ url('client/quotes/' . $quote->id . '/whatsapp') }}" method="POST" onsubmit="return confirm('{{ __('app.client.quotes.confirm_send') }}')">
                    @csrf
                    <button type="submit" class="btn btn-outline">📲 {{ __('app.client.quotes.send_whatsapp') }}</button>
                </form>
            @endif

            <form action="{{ url('client/quotes/' . $quote->id . '/convert') }}" method="POST" onsubmit="return confirm('{{ __('app.client.quotes.confirm_convert') }}')">
                @csrf
                <button type="submit" class="btn btn-primary">→ {{ __('app.client.quotes.convert_invoice') }}</button>
            </form>
        @endif

        <a href="{{ url('client/quotes/' . $quote->id . '/pdf') }}" class="btn btn-outline" target="_blank">📥 {{ __('app.client.quotes.download_pdf') }}</a>

        @if(!in_array($quote->status, ['accepted', 'declined']))
            <form action="{{ url('client/quotes/' . $quote->id) }}" method="POST" style="margin-left:auto;" onsubmit="return confirm('{{ __('app.client.quotes.confirm_delete') }}')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">{{ __('app.client.quotes.delete') }}</button>
            </form>
        @endif
    </div>
</div>
@endsection
