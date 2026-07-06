@extends('client.layout')
@section('title', __('app.client.services.title'))

@section('topbar-right')
    <a href="{{ url('client/services/create') }}" class="btn btn-primary">+ {{ __('app.client.services.new') }}</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h2>{{ __('app.client.services.title_all') }} <span class="badge">{{ $services->count() }}</span></h2>
    </div>

    @if($services->isEmpty())
        <div class="empty">
            <div class="empty-icon">📦</div>
            <p>{{ __('app.client.services.empty') }}</p>
            <a href="{{ url('client/services/create') }}" class="btn btn-primary">+ {{ __('app.client.services.create') }}</a>
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th>{{ __('app.client.services.name') }}</th>
                    <th>{{ __('app.client.services.description') }}</th>
                    <th>{{ __('app.client.services.price') }}</th>
                    <th>{{ __('app.client.services.unit') }}</th>
                    <th>{{ __('app.client.services.active') }}</th>
                    <th>{{ __('app.client.services.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($services as $service)
                <tr>
                    <td style="font-weight:600;">{{ $service->name }}</td>
                    <td>{{ Str::limit(strip_tags($service->description ?? ''), 50) ?: '-' }}</td>
                    <td>{{ number_format($service->unit_price, 2, ',', ' ') }} {{ $business->currency ?? 'XAF' }}</td>
                    <td>{{ $service->unit }}</td>
                    <td><span class="status {{ $service->is_active ? 'active' : 'cancelled' }}">{{ $service->is_active ? __('app.client.common.yes') : __('app.client.common.no') }}</span></td>
                    <td>
                        <a href="{{ url('client/services/' . $service->id . '/edit') }}" class="btn btn-ghost btn-sm">✏️</a>
                        <form action="{{ url('client/services/' . $service->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('{{ __('app.client.services.confirm_delete') }}')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--red);">🗑️</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
