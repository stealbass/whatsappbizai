@extends('client.layout')
@section('title', 'Conversations IA')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Conversations</h2>
    </div>

    @if($conversations->isEmpty())
        <div class="empty">
            <div class="empty-icon">💬</div>
            <p>Aucune conversation pour le moment</p>
            <p style="font-size:13px;color:var(--gray);">Les conversations IA apparaîtront ici lorsque vos contacts interagiront via WhatsApp.</p>
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th>Contact</th>
                    <th>Dernier message</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($conversations as $conv)
                <tr>
                    <td style="font-weight:600;">{{ $conv->contact->name ?? '-' }}</td>
                    <td>{{ Str::limit($conv->last_message ?? '-', 60) }}</td>
                    <td>{{ $conv->updated_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ url('client/conversations/' . $conv->id) }}" class="btn btn-ghost btn-sm">👁️ Voir</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-top:16px;">{{ $conversations->links() }}</div>
    @endif
</div>
@endsection
