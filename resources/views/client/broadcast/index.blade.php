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
            <textarea name="message" id="message" rows="5" required maxlength="1024" placeholder="{{ __('app.client.broadcast.message_placeholder') }}"></textarea>
            <p class="form-help">{{ __('app.client.broadcast.variables') }} : <code>{!! '{{nom}}' !!}</code>, <code>{!! '{{prenom}}' !!}</code>, <code>{!! '{{entreprise}}' !!}</code></p>
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
<script>
document.getElementById('draftBtn').addEventListener('click', async function() {
    const goal = document.getElementById('aiGoal').value || 'Promote our services';
    const target = document.getElementById('target').value;

    this.disabled = true;
    this.textContent = '⏳ {{ __('app.client.broadcast.sending') }}';

    try {
        const response = await fetch('{{ url("client/broadcast/draft-ai") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ goal, target })
        });

        const data = await response.json();
        if (data.message) {
            document.getElementById('message').value = data.message;
        }
    } catch (e) {
        alert('{{ __('app.client.broadcast.draft_error') }}');
    }

    this.disabled = false;
    this.textContent = '🤖 {{ __('app.client.broadcast.draft_ai') }}';
});
</script>
@endsection
