@extends('client.layout')
@section('title', __('app.client.retention.title'))

@section('content')
<div class="card" style="max-width:800px;">
    <div class="card-header">
        <h2>❤️ {{ __('app.client.retention.title') }}</h2>
    </div>

    <form action="{{ url('client/retention') }}" method="POST" id="retentionForm">
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
        </div>

        <div style="display:flex;gap:12px;margin-top:20px;">
            <button type="button" class="btn btn-outline" id="draftBtn">🤖 {{ __('app.client.retention.draft_ai') }}</button>
            <button type="submit" class="btn btn-primary">📤 {{ __('app.client.retention.send') }}</button>
        </div>
    </form>
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
<style>
.html-editor-wrap { border:1px solid #d1d5db; border-radius:8px; overflow:hidden; background:#fff; }
.html-editor-tabs { display:flex; background:#f1f5f9; border-bottom:1px solid #d1d5db; }
.html-editor-tab { padding:8px 16px; font-size:13px; font-weight:600; cursor:pointer; border:none; background:none; color:#64748b; }
.html-editor-tab.active { background:#fff; color:#0ea5e9; border-bottom:2px solid #0ea5e9; }
.html-editor-source { width:100%; min-height:250px; font-family:monospace; font-size:13px; border:none; padding:12px; resize:vertical; display:block; background:#1e293b; color:#e2e8f0; }
.html-editor-preview { width:100%; min-height:250px; border:none; display:none; background:#fff; }
.html-editor-preview.active { display:block; }
#previewFrame { width:100%; min-height:300px; border:none; }
</style>

<div class="html-editor-wrap">
    <div class="html-editor-tabs">
        <button type="button" class="html-editor-tab active" onclick="switchTab('source', this)">📝 Code source</button>
        <button type="button" class="html-editor-tab" onclick="switchTab('preview', this)">👁 Aperçu</button>
    </div>
    <textarea class="html-editor-source active" id="htmlSource" placeholder="Collez votre code HTML ici..."></textarea>
    <div class="html-editor-preview" id="previewPane">
        <iframe id="previewFrame"></iframe>
    </div>
</div>
<input type="hidden" name="message" id="message" required maxlength="100000">
<p class="form-help" style="margin-top:8px;">{{ __('app.client.retention.variables') }} : <code>{!! '{{nom}}' !!}</code>, <code>{!! '{{prenom}}' !!}</code>, <code>{!! '{{entreprise}}' !!}</code></p>

<script>
function switchTab(tab, el) {
    document.querySelectorAll('.html-editor-tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    const source = document.getElementById('htmlSource');
    const preview = document.getElementById('previewPane');
    if (tab === 'preview') {
        const iframe = document.getElementById('previewFrame');
        iframe.srcdoc = source.value;
        preview.classList.add('active');
        source.style.display = 'none';
    } else {
        preview.classList.remove('active');
        source.style.display = 'block';
    }
}

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
        if (data.message) document.getElementById('htmlSource').value = data.message;
    } catch (e) { alert('{{ __("app.client.retention.error") }}'); }
    this.disabled = false;
    this.textContent = '🤖 {{ __("app.client.retention.draft_ai") }}';
});

document.getElementById('retentionForm').addEventListener('submit', function() {
    document.getElementById('message').value = document.getElementById('htmlSource').value;
});
</script>
@endsection
