@extends('client.layout')
@section('title', __('app.client.test_chat.title'))

@section('content')
<div style="display:flex;flex-direction:column;height:calc(100vh - 120px);max-width:800px;">

    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
        <div>
            <h1 style="font-size:20px;font-weight:800;margin:0;">{{ __('app.client.test_chat.title') }}</h1>
            <p style="font-size:13px;color:var(--gray);margin:4px 0 0;">{{ __('app.client.test_chat.subtitle') }}</p>
        </div>
        <div style="display:flex;gap:8px;">
            @if($messages->count() > 0)
            <a href="{{ url('client/test-chat/clear') }}"
               onclick="return confirm('{{ __('app.client.test_chat.confirm_clear') }}')"
               class="btn btn-outline btn-sm" style="color:var(--red);border-color:var(--red);">
                🗑 {{ __('app.client.test_chat.clear') }}
            </a>
            @endif
            <a href="{{ url('client/settings/whatsapp') }}" class="btn btn-outline btn-sm">
                ⚙️ {{ __('app.client.test_chat.settings') }}
            </a>
        </div>
    </div>

    {{-- Sandbox mode badge --}}
    @if(!$business->sandbox_mode)
    <div style="background:#dcfce7;border:1px solid #86efac;border-radius:8px;padding:10px 16px;margin-bottom:12px;font-size:13px;color:#166534;">
        ✅ {{ __('app.client.test_chat.connected_mode') }}
    </div>
    @endif

    {{-- Chat container --}}
    <div id="chat-box" style="flex:1;overflow-y:auto;background:#fff;border:1px solid var(--border);border-radius:12px;padding:20px;margin-bottom:12px;display:flex;flex-direction:column;gap:12px;">
        @if($messages->isEmpty())
        <div class="empty" style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;">
            <div class="empty-icon">🤖</div>
            <p>{{ __('app.client.test_chat.empty') }}</p>
            <p style="font-size:12px;color:var(--gray);">{{ __('app.client.test_chat.empty_hint') }}</p>
        </div>
        @endif

        @foreach($messages as $msg)
            <div style="display:flex;justify-content:{{ $msg->trigger === 'test_chat' ? 'flex-end' : 'flex-start' }};">
                <div style="max-width:75%;padding:10px 14px;border-radius:12px;font-size:14px;{{ $msg->trigger === 'test_chat' ? 'background:var(--sky);color:#fff;' : 'background:#f1f5f9;border:1px solid var(--border);' }}">
                    <div style="font-size:11px;font-weight:600;margin-bottom:4px;{{ $msg->trigger === 'test_chat' ? 'color:rgba(255,255,255,.7);' : 'color:var(--gray);' }}">
                        {{ $msg->trigger === 'test_chat' ? $user->name : '🤖 AI' }}
                    </div>
                    <div style="white-space:pre-line;">{{ $msg->content }}</div>
                    <div style="font-size:10px;margin-top:4px;{{ $msg->trigger === 'test_chat' ? 'color:rgba(255,255,255,.6);' : 'color:var(--gray);' }}">
                        {{ $msg->created_at->format('H:i') }}
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Typing indicator --}}
        <div id="typing" style="display:none;justify-content:flex-start;">
            <div style="max-width:75%;padding:10px 14px;border-radius:12px;background:#f1f5f9;border:1px solid var(--border);font-size:14px;">
                <div style="font-size:11px;font-weight:600;margin-bottom:4px;color:var(--gray);">🤖 AI</div>
                <div style="color:var(--gray);font-style:italic;">{{ __('app.client.test_chat.typing') }}</div>
            </div>
        </div>
    </div>

    {{-- Input --}}
    <form id="chat-form" style="display:flex;gap:8px;">
        @csrf
        <input type="text" id="chat-input" name="message"
               placeholder="{{ __('app.client.test_chat.placeholder') }}"
               autocomplete="off"
               style="flex:1;padding:12px 16px;border:1px solid var(--border);border-radius:10px;font-size:14px;font-family:inherit;"
               autofocus>
        <button type="submit" id="send-btn" class="btn btn-primary" style="padding:12px 20px;">
            {{ __('app.client.test_chat.send') }}
        </button>
    </form>
</div>
@endsection

@section('scripts')
<script>
const chatBox = document.getElementById('chat-box');
const chatForm = document.getElementById('chat-form');
const chatInput = document.getElementById('chat-input');
const typing = document.getElementById('typing');
const sendBtn = document.getElementById('send-btn');

chatBox.scrollTop = chatBox.scrollHeight;

chatForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const msg = chatInput.value.trim();
    if (!msg) return;

    // Add user message to UI
    appendMessage('{{ addslashes($user->name) }}', msg, true);
    chatInput.value = '';
    typing.style.display = 'flex';
    chatBox.scrollTop = chatBox.scrollHeight;
    sendBtn.disabled = true;

    fetch('{{ url("client/test-chat/send") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ message: msg })
    })
    .then(r => r.json())
    .then(data => {
        typing.style.display = 'none';
        if (data.success) {
            appendMessage('🤖 AI', data.reply, false);
        } else {
            appendMessage('🤖 AI', '{{ __("app.client.test_chat.no_reply") }}', false);
        }
        sendBtn.disabled = false;
        chatInput.focus();
    })
    .catch(() => {
        typing.style.display = 'none';
        appendMessage('🤖 AI', '{{ __("app.client.test_chat.error") }}', false);
        sendBtn.disabled = false;
        chatInput.focus();
    });
});

function appendMessage(name, text, isUser) {
    const div = document.createElement('div');
    div.style.cssText = `display:flex;justify-content:${isUser ? 'flex-end' : 'flex-start'};`;
    div.innerHTML = `
        <div style="max-width:75%;padding:10px 14px;border-radius:12px;font-size:14px;${isUser ? 'background:var(--sky);color:#fff;' : 'background:#f1f5f9;border:1px solid var(--border);'}">
            <div style="font-size:11px;font-weight:600;margin-bottom:4px;${isUser ? 'color:rgba(255,255,255,.7);' : 'color:var(--gray);'}">${name}</div>
            <div style="white-space:pre-line;">${escapeHtml(text)}</div>
            <div style="font-size:10px;margin-top:4px;${isUser ? 'color:rgba(255,255,255,.6);' : 'color:var(--gray);'}">${new Date().toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'})}</div>
        </div>
    `;
    // Insert before typing indicator
    chatBox.insertBefore(div, typing);
    chatBox.scrollTop = chatBox.scrollHeight;
}

function escapeHtml(t) {
    const d = document.createElement('div');
    d.textContent = t;
    return d.innerHTML;
}
</script>
@endsection
