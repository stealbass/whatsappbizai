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
        </div>

        <div style="display:flex;gap:12px;margin-top:20px;">
            <button type="button" class="btn btn-outline" id="draftBtn">🤖 {{ __('app.client.broadcast.draft_ai') }}</button>
            <button type="submit" class="btn btn-primary">📤 {{ __('app.client.broadcast.submit') }}</button>
        </div>
    </form>
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
<style>
.html-editor-wrap { border:1px solid #d1d5db; border-radius:8px; overflow:hidden; background:#fff; }
.html-editor-tabs { display:flex; background:#f1f5f9; border-bottom:1px solid #d1d5db; }
.html-editor-tab { padding:8px 16px; font-size:13px; font-weight:600; cursor:pointer; border:none; background:none; color:#64748b; }
.html-editor-tab.active { background:#fff; color:#0ea5e9; border-bottom:2px solid #0ea5e9; }
.html-editor-source { width:100%; min-height:250px; font-family:monospace; font-size:13px; border:none; padding:12px; resize:vertical; display:block; background:#1e293b; color:#e2e8f0; }
.html-editor-preview { width:100%; min-height:250px; border:none; display:none; background:#fff; }
.html-editor-preview.active { display:block; }
.html-editor-source.active { display:block; }
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
<p class="form-help" style="margin-top:8px;">{{ __('app.client.broadcast.variables') }} : <code>{!! '{{nom}}' !!}</code>, <code>{!! '{{prenom}}' !!}</code>, <code>{!! '{{entreprise}}' !!}</code></p>

<script>
function switchTab(tab, el) {
    document.querySelectorAll('.html-editor-tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    const source = document.getElementById('htmlSource');
    const preview = document.getElementById('previewPane');
    if (tab === 'preview') {
        const html = source.value;
        const iframe = document.getElementById('previewFrame');
        iframe.srcdoc = html;
        preview.classList.add('active');
        source.style.display = 'none';
    } else {
        preview.classList.remove('active');
        source.style.display = 'block';
    }
}

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
        if (data.message) document.getElementById('htmlSource').value = data.message;
    } catch (e) { alert('{{ __("app.client.broadcast.draft_error") }}'); }
    this.disabled = false;
    this.textContent = '🤖 {{ __("app.client.broadcast.draft_ai") }}';
});

document.getElementById('broadcastForm').addEventListener('submit', function() {
    document.getElementById('message').value = document.getElementById('htmlSource').value;
});
</script>
@endsection
