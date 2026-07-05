@extends('client.layout')
@section('title', __('app.client.settings.billing.title'))

@section('content')
<div class="card" style="max-width:700px;">
    <div class="card-header"><h2>{{ __('app.client.settings.billing.title') }}</h2></div>

    <div style="padding:20px;background:#f0f9ff;border:1px solid #bae6fd;border-radius:8px;margin-bottom:24px;">
        <div style="display:flex;align-items:center;justify-content:space-between;">
            <div>
                <p style="font-size:12px;color:var(--gray);margin-bottom:4px;">{{ __('app.client.settings.billing.current_plan') }}</p>
                <p style="font-size:20px;font-weight:800;">{{ ucfirst($business->plan ?? 'Gratuit') }}</p>
            </div>
            <a href="{{ url('pricing') }}" class="btn btn-primary">Changer de plan</a>
        </div>
    </div>

    <h3 style="font-size:15px;font-weight:700;margin-bottom:16px;">{{ __('app.client.settings.billing.history') }}</h3>

    @if(!isset($subscriptions) || $subscriptions->isEmpty())
        <div class="empty">
            <p>{{ __('app.client.settings.billing.no_subscriptions') }}</p>
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th>Plan</th>
                    <th>Début</th>
                    <th>Fin</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($subscriptions as $sub)
                <tr>
                    <td style="font-weight:600;">{{ ucfirst($sub->plan) }}</td>
                    <td>{{ \Carbon\Carbon::parse($sub->starts_at)->format('d/m/Y') }}</td>
                    <td>{{ $sub->ends_at ? \Carbon\Carbon::parse($sub->ends_at)->format('d/m/Y') : '-' }}</td>
                    <td><span class="status {{ $sub->status }}">{{ ucfirst($sub->status) }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
