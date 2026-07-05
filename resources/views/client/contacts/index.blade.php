@extends('client.layout')
@section('title', 'Contacts')

@section('topbar-right')
    <a href="{{ url('client/contacts/create') }}" class="btn btn-primary">+ Nouveau contact</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Tous les contacts <span class="badge">{{ $contacts->total() }}</span></h2>
    </div>

    @if($contacts->isEmpty())
        <div class="empty">
            <div class="empty-icon">👥</div>
            <p>Aucun contact pour le moment</p>
            <a href="{{ url('client/contacts/create') }}" class="btn btn-primary">+ Ajouter un contact</a>
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>WhatsApp</th>
                    <th>Email</th>
                    <th>Entreprise</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contacts as $c)
                <tr>
                    <td style="font-weight:600;">{{ $c->name }}</td>
                    <td>{{ $c->whatsapp_number }}</td>
                    <td>{{ $c->email ?? '-' }}</td>
                    <td>{{ $c->company ?? '-' }}</td>
                    <td><span class="status {{ $c->status === 'client' ? 'active' : ($c->status === 'prospect' ? 'sent' : 'draft') }}">{{ ucfirst($c->status) }}</span></td>
                    <td>
                        <a href="{{ url('client/contacts/' . $c->id . '/edit') }}" class="btn btn-ghost btn-sm">✏️</a>
                        <form action="{{ url('client/contacts/' . $c->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Supprimer ce contact ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--red);">🗑️</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-top:16px;">{{ $contacts->links() }}</div>
    @endif
</div>
@endsection
