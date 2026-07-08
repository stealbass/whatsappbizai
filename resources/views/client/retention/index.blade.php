@extends('client.layout')
@section('title', __('app.client.retention.title'))

@section('content')
<div class="card" style="max-width:800px;">
    <div class="card-header">
        <h2>❤️ {{ __('app.client.retention.title') }}</h2>
    </div>

    <form action="{{ route('c.retention.send') }}" method="POST" id="retentionForm">
        @csrf

        <div class="form-row">
            <div class="form-group">
                <label>{{ __('app.client.retention.campaign_type') }} *</label>
                <select name="objective" required>
                    <option value="retention">🔒 {{ __('app.client.retention.type_retention') }}</option>
                    <option value="upsell">📈 {{ __('app.client.retention.type_upsell') }}</option>
                    <option value="winback">🔄 {{ __('app.client.retention.type_winback') }}</option>
                    <option value="referral">👥 {{ __('app.client.retention.type_referral') }}</option>
                </select>
            </div>
            <div class="form-group">
                <label>{{ __('app.client.retention.recipients') }} *</label>
                <select name="target" required>
                    <option value="inactive_clients">{{ __('app.client.retention.rec_inactive') }} ({{ $stats['inactive'] }})</option>
                    <option value="all_clients">{{ __('app.client.retention.rec_all') }} ({{ $stats['clients'] }})</option>
                    <option value="prospects">{{ __('app.client.retention.rec_prospects') }} ({{ $stats['prospects'] }})</option>
                    <option value="high_value">{{ __('app.client.retention.rec_high_value') }} ({{ $stats['high_value'] }})</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>{{ __('app.client.retention.objective') }}</label>
                <input type="text" id="aiGoal" placeholder="{{ __('app.client.retention.objective_placeholder') }}">
            </div>
            <div></div>
        </div>

        <div class="form-group">
                <label>{{ __('app.client.retention.message') }} *</label>
            <textarea name="message" id="message"  required maxlength="100000" placeholder="{{ __('app.client.retention.message_placeholder') }}"></textarea>
            <p class="form-help">{{ __('app.client.retention.variables') }} : <code>@php echo '{{nom}}' @endphp</code>, <code>@php echo '{{prenom}}' @endphp</code>, <code>@php echo '{{entreprise}}' @endphp</code></p>
        </div>

        <div style="display:flex;gap:12px;margin-top:20px;flex-wrap:wrap;">
            <button type="button" class="btn btn-outline" id="draftBtn">🤖 {{ __('app.client.retention.draft_ai') }}</button>
            <button type="button" class="btn btn-outline" id="previewBtn" style="border-color:#6366f1;color:#6366f1;">👁️ Aperçu</button>
            <button type="submit" class="btn btn-primary">📤 {{ __('app.client.retention.send') }}</button>
        </div>
    </form>

{{-- ── Preview Modal ──────────────────────────────────────────── --}}
<div id="previewModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.55);overflow-y:auto;">
    <div style="max-width:680px;margin:40px auto;background:#fff;border-radius:12px;box-shadow:0 20px 60px rgba(0,0,0,.3);overflow:hidden;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;background:#f8fafc;border-bottom:1px solid #e2e8f0;">
            <span style="font-weight:700;font-size:15px;">👁️ Aperçu de l'email</span>
            <div style="display:flex;gap:8px;align-items:center;">
                <span style="font-size:11px;color:#64748b;background:#e2e8f0;padding:3px 8px;border-radius:99px;">Rendu réel reçu par le destinataire</span>
                <button onclick="document.getElementById('previewModal').style.display='none'" style="background:none;border:none;cursor:pointer;font-size:20px;color:#94a3b8;line-height:1;">✕</button>
            </div>
        </div>
        {{-- Email envelope preview --}}
        <div style="padding:16px 20px;background:#e9ecef;border-bottom:1px solid #dee2e6;font-size:12px;color:#6c757d;font-family:monospace;">
            <div><strong>De :</strong> {{ config('mail.from.name', 'WhatsAppBizAI') }} &lt;{{ config('mail.from.address', 'noreply@example.com') }}&gt;</div>
            <div><strong>Objet :</strong> <span id="previewSubject">Campagne marketing</span></div>
        </div>
        {{-- Rendered HTML content in sandboxed iframe --}}
        <div style="padding:24px;min-height:300px;">
            <iframe id="previewIframe"
                style="width:100%;border:1px solid #e2e8f0;border-radius:8px;min-height:400px;"
                sandbox="allow-same-origin"
                title="Aperçu email"></iframe>
        </div>
        <div style="padding:16px 20px;background:#f8fafc;border-top:1px solid #e2e8f0;text-align:right;">
            <button onclick="document.getElementById('previewModal').style.display='none'" class="btn btn-outline" style="margin-right:8px;">Fermer</button>
            <button onclick="document.getElementById('previewModal').style.display='none';document.getElementById('retentionForm').dispatchEvent(new Event('submit',{bubbles:true,cancelable:true}));" class="btn btn-primary">📤 Envoyer maintenant</button>
        </div>
    </div>
</div>
</div>

<div class="card" style="max-width:800px;margin-top:24px;">
    <h3 style="font-size:15px;font-weight:700;margin-bottom:12px;">💡 {{ __('app.client.retention.strategies_title') }}</h3>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div style="background:var(--light);padding:12px;border-radius:8px;">
            <p style="font-weight:600;font-size:13px;">🔒 {{ __('app.client.retention.strat_retention') }}</p>
            <p style="font-size:12px;color:var(--gray);">{{ __('app.client.retention.strat_retention_desc') }}</p>
        </div>
        <div style="background:var(--light);padding:12px;border-radius:8px;">
            <p style="font-weight:600;font-size:13px;">📈 {{ __('app.client.retention.strat_upsell') }}</p>
            <p style="font-size:12px;color:var(--gray);">{{ __('app.client.retention.strat_upsell_desc') }}</p>
        </div>
        <div style="background:var(--light);padding:12px;border-radius:8px;">
            <p style="font-weight:600;font-size:13px;">🔄 {{ __('app.client.retention.strat_winback') }}</p>
            <p style="font-size:12px;color:var(--gray);">{{ __('app.client.retention.strat_winback_desc') }}</p>
        </div>
        <div style="background:var(--light);padding:12px;border-radius:8px;">
            <p style="font-weight:600;font-size:13px;">👥 {{ __('app.client.retention.strat_referral') }}</p>
            <p style="font-size:12px;color:var(--gray);">{{ __('app.client.retention.strat_referral_desc') }}</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@include('components.quill')
<script>
initQuill('#message', 350);

// ── Aperçu modal ────────────────────────────────────────────────────────────
document.getElementById('previewBtn').addEventListener('click', function() {
    // Read from the hidden textarea — truest raw value, preserves <!DOCTYPE html>
    var ta   = document.querySelector('#message');
    var html = ta ? ta.value : '';
    if (!html || html === '<p><br></p>') {
        alert('Rédigez un message avant de prévisualiser.');
        return;
    }

    // Full HTML doc → inject as-is; fragment → wrap in email shell
    var isFullDoc = /^\s*<!DOCTYPE/i.test(html) || /^\s*<html/i.test(html);
    var iframeContent = isFullDoc ? html : `<!DOCTYPE html><html><head>
        <meta charset="utf-8">
        <style>
            body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                   font-size: 14px; line-height: 1.7; color: #1e293b;
                   max-width: 600px; margin: 0 auto; padding: 24px; }
            h1,h2,h3 { color: #0f172a; }
            a { color: #0ea5e9; }
            blockquote { border-left: 3px solid #e2e8f0; margin-left: 0; padding-left: 16px; color: #64748b; }
            pre,code { background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-size: 13px; }
            ul,ol { padding-left: 24px; } img { max-width: 100%; }
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

// Close modal on backdrop click
document.getElementById('previewModal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.style.display = 'none';
        document.body.style.overflow = '';
    }
});

// Restore scroll when modal closes
document.querySelectorAll('[onclick*="previewModal"]').forEach(function(btn) {
    btn.addEventListener('click', function() { document.body.style.overflow = ''; });
});

// ── Draft AI ─────────────────────────────────────────────────────────────────
document.getElementById('draftBtn').addEventListener('click', async function() {
    const goal = document.getElementById('aiGoal').value || 'Retention message';
    const form = document.getElementById('retentionForm') || this.closest('form');
    const objective = form.querySelector('[name="objective"]').value;
    const target = form.querySelector('[name="target"]').value;
    this.disabled = true;
    this.textContent = '⏳ {{ __("app.client.retention.sending") }}';
    try {
        const response = await fetch('{{ url("client/retention/draft-ai") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({ goal, objective, target })
        });
        const data = await response.json();
        if (data.message) setQuillContent('#message', data.message);
    } catch (e) { alert('{{ __("app.client.retention.error") }}'); }
    this.disabled = false;
    this.textContent = '🤖 {{ __("app.client.retention.draft_ai") }}';
});
</script>
@endsection
