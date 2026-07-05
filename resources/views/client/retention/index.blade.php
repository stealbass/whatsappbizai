@extends('client.layout')
@section('title', 'Rétention & Acquisition')

@section('content')
<div class="card" style="max-width:800px;">
    <div class="card-header">
        <h2>❤️ Rétention & Acquisition</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <form action="{{ url('client/retention') }}" method="POST">
        @csrf

        <div class="form-row">
            <div class="form-group">
                <label>Type de campagne *</label>
                <select name="objective" required>
                    <option value="retention">🔒 Rétention — Fidéliser les clients</option>
                    <option value="upsell">📈 Upsell — Proposer services premium</option>
                    <option value="winback">🔄 Win-back — Réactiver les inactifs</option>
                    <option value="referral">👥 Parrainage — Obtenir de nouveaux clients</option>
                </select>
            </div>
            <div class="form-group">
                <label>Destinataires *</label>
                <select name="target" required>
                    <option value="inactive_clients">Clients inactifs 30+ jours ({{ $stats['inactive'] }})</option>
                    <option value="all_clients">Tous les clients actifs ({{ $stats['clients'] }})</option>
                    <option value="prospects">Prospects ({{ $stats['prospects'] }})</option>
                    <option value="high_value">Clients à forte valeur ({{ $stats['high_value'] }})</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Objectif IA (optionnel)</label>
                <input type="text" id="aiGoal" placeholder="Ex: -20% sur les services premium cette semaine">
            </div>
            <div></div>
        </div>

        <div class="form-group">
            <label>Message *</label>
            <textarea name="message" id="message" rows="5" required maxlength="1024" placeholder="Bonjour {{prenom}},&#10;&#10;Nous avons une offre spéciale pour vous..."></textarea>
            <p class="form-help">Variables : <code>{!! '{{nom}}' !!}</code>, <code>{!! '{{prenom}}' !!}</code>, <code>{!! '{{entreprise}}' !!}</code></p>
        </div>

        <div style="display:flex;gap:12px;margin-top:20px;">
            <button type="button" class="btn btn-outline" id="draftBtn">🤖 Rédiger avec l'IA</button>
            <button type="submit" class="btn btn-primary">📤 Envoyer la campagne</button>
        </div>
    </form>
</div>

<div class="card" style="max-width:800px;margin-top:24px;">
    <h3 style="font-size:15px;font-weight:700;margin-bottom:12px;">💡 Stratégies de rétention</h3>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div style="background:var(--light);padding:12px;border-radius:8px;">
            <p style="font-weight:600;font-size:13px;">🔒 Rétention</p>
            <p style="font-size:12px;color:var(--gray);">Offre de fidélité après 3 achats</p>
        </div>
        <div style="background:var(--light);padding:12px;border-radius:8px;">
            <p style="font-weight:600;font-size:13px;">📈 Upsell</p>
            <p style="font-size:12px;color:var(--gray);">Proposer un upgrade de plan</p>
        </div>
        <div style="background:var(--light);padding:12px;border-radius:8px;">
            <p style="font-weight:600;font-size:13px;">🔄 Win-back</p>
            <p style="font-size:12px;color:var(--gray);">Message après 30 jours d'inactivité</p>
        </div>
        <div style="background:var(--light);padding:12px;border-radius:8px;">
            <p style="font-weight:600;font-size:13px;">👥 Parrainage</p>
            <p style="font-size:12px;color:var(--gray);">Programme avec récompense</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('draftBtn').addEventListener('click', async function() {
    const goal = document.getElementById('aiGoal').value || 'Retention message';
    const form = document.getElementById('retentionForm') || this.closest('form');
    const objective = form.querySelector('[name="objective"]').value;
    const target = form.querySelector('[name="target"]').value;

    this.disabled = true;
    this.textContent = '⏳ Génération en cours...';

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
            document.getElementById('message').value = data.message;
        }
    } catch (e) {
        alert('Erreur lors de la génération du message.');
    }

    this.disabled = false;
    this.textContent = '🤖 Rédiger avec l\'IA';
});
</script>
@endsection
