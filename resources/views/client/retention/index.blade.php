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
            <div id="editor-container" style="background:#fff;border:1px solid #d1d5db;border-radius:8px;min-height:150px;"></div>
            <input type="hidden" name="message" id="message" required maxlength="1024">
            <p class="form-help">{{ __('app.client.retention.variables') }} : <code>{!! '{{nom}}' !!}</code>, <code>{!! '{{prenom}}' !!}</code>, <code>{!! '{{entreprise}}' !!}</code></p>
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
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
const quill = new Quill('#editor-container', {
    theme: 'snow',
    placeholder: '{{ __("app.client.retention.message_placeholder") }}',
    modules: {
        toolbar: [
            ['bold', 'italic', 'underline'],
            ['link'],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }]
        ]
    }
});

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
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ goal, objective, target })
        });

        const data = await response.json();
        if (data.message) {
            quill.root.innerHTML = data.message;
        }
    } catch (e) {
        alert('{{ __("app.client.retention.error") }}');
    }

    this.disabled = false;
    this.textContent = '🤖 {{ __("app.client.retention.draft_ai") }}';
});

document.getElementById('retentionForm').addEventListener('submit', function(e) {
    document.getElementById('message').value = quill.root.innerHTML;
});
</script>
@endsection
