@extends('client.layout')
@section('title', __('app.client.contacts.title'))

@section('topbar-right')
    <a href="{{ url('client/contacts/create') }}" class="btn btn-primary">+ {{ __('app.client.contacts.new') }}</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Tous les contacts <span class="badge">{{ $contacts->total() }}</span></h2>
    </div>

    @if($contacts->isEmpty())
        <div class="empty">
            <div class="empty-icon">👥</div>
            <p>{{ __('app.client.contacts.empty') }}</p>
            <a href="{{ url('client/contacts/create') }}" class="btn btn-primary">+ {{ __('app.client.contacts.create') }}</a>
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th>{{ __('app.client.contacts.name') }}</th>
                    <th>{{ __('app.client.contacts.whatsapp') }}</th>
                    <th>{{ __('app.client.contacts.email') }}</th>
                    <th>{{ __('app.client.contacts.company') }}</th>
                    <th>{{ __('app.client.contacts.status') }}</th>
                    <th>{{ __('app.client.contacts.actions') }}</th>
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
                        <form action="{{ url('client/contacts/' . $c->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('{{ __('app.client.contacts.confirm_delete') }}')">
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
