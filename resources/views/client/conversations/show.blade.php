@extends('client.layout')
@section('title', __('app.client.conversations.title') . ' — ' . ($conversation->contact->name ?? __('app.client.conversations.customer')))

@section('content')
<div class="card" style="max-width:700px;">
    <div class="card-header">
        <h2>{{ __('app.client.conversations.title') }} — {{ $conversation->contact->name ?? __('app.client.conversations.customer') }}</h2>
        <a href="{{ url('client/conversations') }}" class="btn btn-outline btn-sm">{{ __('app.client.conversations.back') }}</a>
    </div>

    <div id="chat-container" style="max-height:500px;overflow-y:auto;padding:16px;background:#f8fafc;border-radius:8px;margin-bottom:16px;">
        @forelse($conversation->messages as $message)
            <div style="display:flex;justify-content:{{ $message->direction === 'inbound' ? 'flex-start' : 'flex-end' }};margin-bottom:12px;">
                <div style="max-width:75%;padding:10px 14px;border-radius:12px;font-size:14px;{{ $message->direction === 'inbound' ? 'background:#fff;border:1px solid var(--border);' : 'background:var(--sky);color:#fff;' }}">
                    <div style="font-size:11px;font-weight:600;margin-bottom:4px;{{ $message->direction === 'inbound' ? 'color:var(--gray);' : 'color:rgba(255,255,255,.7);' }}">
                        {{ $message->direction === 'inbound' ? ($conversation->contact->name ?? __('app.client.conversations.customer')) : __('app.client.conversations.ai_reply') }}
                    </div>
                    <div>{!! nl2br(e($message->content)) !!}</div>
                    <div style="font-size:10px;margin-top:4px;{{ $message->direction === 'inbound' ? 'color:var(--gray);' : 'color:rgba(255,255,255,.6);' }}">
                        {{ $message->created_at->format('H:i') }}
                    </div>
                </div>
            </div>
        @empty
            <div class="empty">
                <p>{{ __('app.client.conversations.no_messages') }}</p>
            </div>
        @endforelse
    </div>
</div>

<script>
const chat = document.getElementById('chat-container');
if (chat) chat.scrollTop = chat.scrollHeight;
</script>
@endsection
