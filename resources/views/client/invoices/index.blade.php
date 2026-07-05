@extends('client.layout')
@section('title', __('app.client.invoices.title'))

@section('topbar-right')
    <a href="{{ url('client/invoices/create') }}" class="btn btn-primary">+ {{ __('app.client.invoices.new') }}</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h2>{{ __('app.client.invoices.title') }} <span class="badge">{{ $invoices->total() }}</span></h2>
    </div>

    @if($invoices->isEmpty())
        <div class="empty">
            <div class="empty-icon">🧾</div>
            <p>{{ __('app.client.invoices.empty') }}</p>
            <a href="{{ url('client/invoices/create') }}" class="btn btn-primary">+ {{ __('app.client.invoices.create') }}</a>
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('app.client.invoices.client') }}</th>
                    <th>{{ __('app.client.invoices.amount') }}</th>
                    <th>{{ __('app.client.invoices.status') }}</th>
                    <th>Date</th>
                    <th>{{ __('app.client.invoices.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $invoice)
                <tr>
                    <td style="font-weight:600;">{{ $invoice->number }}</td>
                    <td>{{ $invoice->contact->name ?? '-' }}</td>
                    <td>{{ number_format($invoice->total, 2, ',', ' ') }} {{ $business->currency ?? 'XAF' }}</td>
                    <td><span class="status {{ $invoice->status }}">
                                @if($invoice->status === 'paid'){{ __('app.client.invoices.status_paid') }}
                                @elseif($invoice->status === 'pending'){{ __('app.client.invoices.status_pending') }}
                                @elseif($invoice->status === 'overdue'){{ __('app.client.invoices.status_overdue') }}
                                @elseif($invoice->status === 'draft'){{ __('app.client.invoices.status_draft') }}
                                @else{{ ucfirst($invoice->status) }}@endif
                            </span></td>
                    <td>{{ \Carbon\Carbon::parse($invoice->issue_date)->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ url('client/invoices/' . $invoice->id) }}" class="btn btn-ghost btn-sm">👁️</a>
                        <form action="{{ url('client/invoices/' . $invoice->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('{{ __('app.client.invoices.confirm_delete') }}')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--red);">🗑️</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-top:16px;">{{ $invoices->links() }}</div>
    @endif
</div>
@endsection
