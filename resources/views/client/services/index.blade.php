@extends('client.layout')
@section('title', 'Services')

@section('topbar-right')
    <a href="{{ url('client/services/create') }}" class="btn btn-primary">+ Nouveau service</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Tous les services <span class="badge">{{ $services->count() }}</span></h2>
    </div>

    @if($services->isEmpty())
        <div class="empty">
            <div class="empty-icon">📦</div>
            <p>Aucun service pour le moment</p>
            <a href="{{ url('client/services/create') }}" class="btn btn-primary">+ Ajouter un service</a>
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Prix unitaire</th>
                    <th>Unité</th>
                    <th>Actif</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($services as $service)
                <tr>
                    <td style="font-weight:600;">{{ $service->name }}</td>
                    <td>{{ Str::limit($service->description, 50) ?? '-' }}</td>
                    <td>{{ number_format($service->unit_price, 2, ',', ' ') }} {{ $business->currency ?? 'XAF' }}</td>
                    <td>{{ $service->unit }}</td>
                    <td><span class="status {{ $service->is_active ? 'active' : 'cancelled' }}">{{ $service->is_active ? 'Oui' : 'Non' }}</span></td>
                    <td>
                        <a href="{{ url('client/services/' . $service->id . '/edit') }}" class="btn btn-ghost btn-sm">✏️</a>
                        <form action="{{ url('client/services/' . $service->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Supprimer ce service ?')">
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
