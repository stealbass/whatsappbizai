@extends('client.layout')
@section('title', __('app.client.quotes.title'))

@section('topbar-right')
    <a href="{{ url('client/quotes/create') }}" class="btn btn-primary">+ {{ __('app.client.quotes.new') }}</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Tous les devis <span class="badge">{{ $quotes->total() }}</span></h2>
    </div>

    @if($quotes->isEmpty())
        <div class="empty">
            <div class="empty-icon">📄</div>
            <p>{{ __('app.client.quotes.empty') }}</p>
            <a href="{{ url('client/quotes/create') }}" class="btn btn-primary">+ {{ __('app.client.quotes.create') }}</a>
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('app.client.quotes.client') }}</th>
                    <th>{{ __('app.client.quotes.amount') }}</th>
                    <th>{{ __('app.client.quotes.status') }}</th>
                    <th>{{ __('app.client.quotes.valid_until') }}</th>
                    <th>{{ __('app.client.quotes.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotes as $quote)
                <tr>
                    <td style="font-weight:600;">{{ $quote->number }}</td>
                    <td>{{ $quote->contact->name ?? '-' }}</td>
                    <td>{{ number_format($quote->total, 2, ',', ' ') }} {{ $business->currency ?? 'XAF' }}</td>
                    <td><span class="status {{ $quote->status }}">{{ ucfirst($quote->status) }}</span></td>
                    <td>{{ $quote->valid_until ? \Carbon\Carbon::parse($quote->valid_until)->format('d/m/Y') : '-' }}</td>
                    <td>
                        <a href="{{ url('client/quotes/' . $quote->id) }}" class="btn btn-ghost btn-sm">👁️</a>
                        <form action="{{ url('client/quotes/' . $quote->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('{{ __('app.client.quotes.confirm_delete') }}')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--red);">🗑️</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-top:16px;">{{ $quotes->links() }}</div>
    @endif
</div>
@endsection
