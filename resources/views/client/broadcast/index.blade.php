@extends('client.layout')
@section('title', __('app.client.broadcast.title'))

@section('content')
<div class="card" style="max-width:800px;">
    <div class="card-header">
        <h2>📤 {{ __('app.client.broadcast.title') }}</h2>
    </div>

    <form action="{{ url('client/broadcast') }}" method="POST" id="broadcastForm">
        @csrf

        <div class="form-row">
            <div class="form-group">
                <label>{{ __('app.client.broadcast.send_to') }}</label>
                <select name="target" id="target" required>
                    <option value="all">{{ __('app.client.broadcast.all') }} ({{ $stats['total'] }})</option>
                    <option value="clients">{{ __('app.client.broadcast.clients') }} ({{ $stats['clients'] }})</option>
                    <option value="prospects">{{ __('app.client.broadcast.prospects') }} ({{ $stats['prospects'] }})</option>
                </select>
            </div>
            <div class="form-group">
                <label>{{ __('app.client.broadcast.ai_goal') }}</label>
                <input type="text" id="aiGoal" placeholder="{{ __('app.client.broadcast.ai_goal_placeholder') }}">
            </div>
        </div>

        <div class="form-group">
            <label>{{ __('app.client.broadcast.message') }}</label>
            <textarea name="message" id="message"  required maxlength="100000" placeholder="{{ __('app.client.broadcast.message_placeholder') }}"></textarea>
            <p class="form-help">{{ __('app.client.broadcast.variables') }} : <code>@php echo '{{nom}}' @endphp</code>, <code>@php echo '{{prenom}}' @endphp</code>, <code>@php echo '{{entreprise}}' @endphp</code></p>
        </div>

        <div style="display:flex;gap:12px;margin-top:20px;flex-wrap:wrap;">
            <button type="button" class="btn btn-outline" id="draftBtn">🤖 {{ __('app.client.broadcast.draft_ai') }}</button>
            <button type="button" class="btn btn-outline" id="previewBtn" style="border-color:#6366f1;color:#6366f1;">👁️ Aperçu</button>
            <button type="submit" class="btn btn-primary">📤 {{ __('app.client.broadcast.submit') }}</button>
        </div>
    </form>

{{-- Preview Modal --}}
<div id="previewModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.55);overflow-y:auto;">
    <div style="max-width:680px;margin:40px auto;background:#fff;border-radius:12px;box-shadow:0 20px 60px rgba(0,0,0,.3);overflow:hidden;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;background:#f8fafc;border-bottom:1px solid #e2e8f0;">
            <span style="font-weight:700;font-size:15px;">👁️ Aperçu du message</span>
            <div style="display:flex;gap:8px;align-items:center;">
                <span style="font-size:11px;color:#64748b;background:#e2e8f0;padding:3px 8px;border-radius:99px;">Rendu réel reçu via WhatsApp / email</span>
                <button onclick="document.getElementById('previewModal').style.display='none';document.body.style.overflow='';" style="background:none;border:none;cursor:pointer;font-size:20px;color:#94a3b8;line-height:1;">✕</button>
            </div>
        </div>
        <div style="padding:16px 20px;background:#e9ecef;border-bottom:1px solid #dee2e6;font-size:12px;color:#6c757d;">
            <strong>Destinataires :</strong> <span id="previewTarget"></span>
        </div>
        <div style="padding:24px;min-height:300px;">
            <iframe id="previewIframe"
                style="width:100%;border:1px solid #e2e8f0;border-radius:8px;min-height:300px;"
                sandbox="allow-same-origin"
                title="Aperçu message"></iframe>
        </div>
        <div style="padding:16px 20px;background:#f8fafc;border-top:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center;">
            <span style="font-size:12px;color:#64748b;">Le rendu WhatsApp est en texte brut. L’aperçu ci-dessus simule la version email.</span>
            <div style="display:flex;gap:8px;">
                <button onclick="document.getElementById('previewModal').style.display='none';document.body.style.overflow='';" class="btn btn-outline">Fermer</button>
                <button id="confirmSendBtn" class="btn btn-primary">📤 Envoyer maintenant</button>
            </div>
        </div>
    </div>
</div>
</div>

<div class="card" style="max-width:800px;margin-top:24px;">
    <h3 style="font-size:15px;font-weight:700;margin-bottom:12px;">💡 {{ __('app.client.broadcast.how_title') }}</h3>
    <ol style="font-size:13px;color:var(--gray);padding-left:20px;space-y:4px;">
        <li>{{ __('app.client.broadcast.how_step_1') }}</li>
        <li>{{ __('app.client.broadcast.how_step_2') }}</li>
        <li>{{ __('app.client.broadcast.how_step_3') }}</li>
        <li>{{ __('app.client.broadcast.how_step_4') }}</li>
    </ol>
    <p style="font-size:12px;color:var(--amber-600);margin-top:12px;">⚠️ {{ __('app.client.broadcast.how_warning') }}</p>
</div>
@endsection

@section('scripts')
@include('components.quill')
<script>
initQuill('#message', 400);

// ── Preview modal ────────────────────────────────────────────────────────────
var targetLabels = {
    all:       '{{ __('app.client.broadcast.all') }} ({{ $stats['total'] }})',
    clients:   '{{ __('app.client.broadcast.clients') }} ({{ $stats['clients'] }})',
    prospects: '{{ __('app.client.broadcast.prospects') }} ({{ $stats['prospects'] }})',
};

document.getElementById('previewBtn').addEventListener('click', function() {
    // Always read from the hidden textarea — it holds the true raw value,
    // including full <!DOCTYPE html> documents that Quill cannot represent.
    var ta  = document.querySelector('#message');
    var html = ta ? ta.value : '';
    if (!html || html === '<p><br></p>') {
        alert('Rédigez un message avant de prévisualiser.');
        return;
    }
    var target = document.getElementById('target').value;
    document.getElementById('previewTarget').textContent = targetLabels[target] || target;

    // If the content is a full HTML document, inject it as-is into the iframe.
    // Otherwise wrap it in a minimal email shell.
    var isFullDoc = /^\s*<!DOCTYPE/i.test(html) || /^\s*<html/i.test(html);
    var iframeContent = isFullDoc ? html : `<!DOCTYPE html><html><head>
        <meta charset="utf-8">
        <style>
            body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                   font-size: 14px; line-height: 1.7; color: #1e293b;
                   max-width: 580px; margin: 0 auto; padding: 20px; }
            h1,h2,h3 { color: #0f172a; }
            a { color: #0ea5e9; }
            blockquote { border-left:3px solid #e2e8f0; margin-left:0; padding-left:16px; color:#64748b; }
            pre,code { background:#f1f5f9; padding:2px 6px; border-radius:4px; font-size:13px; }
            ul,ol { padding-left:24px; } img { max-width:100%; }
        </style>
    </head><body>${html}</body></html>`;

    var iframe = document.getElementById('previewIframe');
    iframe.srcdoc = iframeContent;
    iframe.onload = function() {
        try {
            var h = iframe.contentDocument.body.scrollHeight;
            iframe.style.height = Math.max(300, h + 40) + 'px';
        } catch(e) {}
    };
    document.getElementById('previewModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
});
// Confirm send from modal
document.getElementById('confirmSendBtn').addEventListener('click', function() {
    document.getElementById('previewModal').style.display = 'none';
    document.body.style.overflow = '';
    document.getElementById('broadcastForm').submit();
});

// Close on backdrop click
document.getElementById('previewModal').addEventListener('click', function(e) {
    if (e.target === this) { this.style.display = 'none'; document.body.style.overflow = ''; }
});

// ── Draft AI ─────────────────────────────────────────────────────────────────
document.getElementById('draftBtn').addEventListener('click', async function() {
    const goal = document.getElementById('aiGoal').value || 'Promote our services';
    const target = document.getElementById('target').value;
    this.disabled = true;
    this.textContent = '⏳ {{ __("app.client.broadcast.sending") }}';
    try {
        const response = await fetch('{{ url("client/broadcast/draft-ai") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({ goal, target })
        });
        const data = await response.json();
        if (data.message) setQuillContent('#message', data.message);
    } catch (e) { alert('{{ __("app.client.broadcast.draft_error") }}'); }
    this.disabled = false;
    this.textContent = '🤖 {{ __("app.client.broadcast.draft_ai") }}';
});
</script>
@endsection
