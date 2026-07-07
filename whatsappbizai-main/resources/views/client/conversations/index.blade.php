@extends('client.layout')
@section('title', __('app.client.conversations.title'))

@section('content')
<div class="card">
    <div class="card-header">
        <h2>{{ __('app.client.conversations.title') }}</h2>
    </div>

    @if($conversations->isEmpty())
        <div class="empty">
            <div class="empty-icon">💬</div>
            <p>{{ __('app.client.conversations.empty') }}</p>
            <p style="font-size:13px;color:var(--gray);">{{ __('app.client.conversations.empty_desc') }}</p>
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th>{{ __('app.client.contacts.name') }}</th>
                    <th>{{ __('app.client.conversations.last_message') }}</th>
                    <th>{{ __('app.client.conversations.date') }}</th>
                    <th>{{ __('app.client.contacts.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($conversations as $conv)
                <tr>
                    <td style="font-weight:600;">{{ $conv->contact->name ?? '-' }}</td>
                    <td>{{ Str::limit($conv->lastMessage?->content ?? $conv->summary ?? '-', 60) }}</td>
                    <td>{{ $conv->updated_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ url('client/conversations/' . $conv->id) }}" class="btn btn-ghost btn-sm">👁️ {{ __('app.client.conversations.view') }}</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-top:16px;">{{ $conversations->links() }}</div>
    @endif
</div>
@endsection
