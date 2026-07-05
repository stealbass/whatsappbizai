@extends('client.layout')
@section('title', 'Broadcast WhatsApp')

@section('content')
<div class="card" style="max-width:800px;">
    <div class="card-header">
        <h2>📤 Broadcast WhatsApp</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <form action="{{ url('client/broadcast') }}" method="POST" id="broadcastForm">
        @csrf

        <div class="form-row">
            <div class="form-group">
                <label>Envoyer à *</label>
                <select name="target" id="target" required>
                    <option value="all">Tous les contacts ({{ $stats['total'] }})</option>
                    <option value="clients">Clients uniquement ({{ $stats['clients'] }})</option>
                    <option value="prospects">Prospects uniquement ({{ $stats['prospects'] }})</option>
                </select>
            </div>
            <div class="form-group">
                <label>Objectif IA (optionnel)</label>
                <input type="text" id="aiGoal" placeholder="Ex: Annonce promotion 20% sur nos services">
            </div>
        </div>

        <div class="form-group">
            <label>Message *</label>
            <textarea name="message" id="message" rows="5" required maxlength="1024" placeholder="Bonjour {{prenom}},&#10;&#10;Nous avons le plaisir de vous annoncer...&#10;&#10;Cordialement,{{entreprise}}"></textarea>
            <p class="form-help">Variables disponibles : <code>{{'{{nom}}'}}</code>, <code>{{'{{prenom}}'}}</code>, <code>{{'{{entreprise}}'}}</code></p>
        </div>

        <div style="display:flex;gap:12px;margin-top:20px;">
            <button type="button" class="btn btn-outline" id="draftBtn">🤖 Rédiger avec l'IA</button>
            <button type="submit" class="btn btn-primary">📤 Envoyer le broadcast</button>
        </div>
    </form>
</div>

<div class="card" style="max-width:800px;margin-top:24px;">
    <h3 style="font-size:15px;font-weight:700;margin-bottom:12px;">💡 Comment ça marche</h3>
    <ol style="font-size:13px;color:var(--gray);padding-left:20px;space-y:4px;">
        <li>Sélectionnez vos destinataires (tous, clients ou prospects)</li>
        <li>Rédigez votre message ou utilisez l'IA pour le générer</li>
        <li>Vérifiez et modifiez le message si nécessaire</li>
        <li>Cliquez sur "Envoyer le broadcast"</li>
    </ol>
    <p style="font-size:12px;color:var(--amber-600);margin-top:12px;">⚠️ WhatsApp nécessite des templates approuvés pour l'envoi en masse.</p>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('draftBtn').addEventListener('click', async function() {
    const goal = document.getElementById('aiGoal').value || 'Promote our services';
    const target = document.getElementById('target').value;

    this.disabled = true;
    this.textContent = '⏳ Génération en cours...';

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
        alert('Erreur lors de la génération du message.');
    }

    this.disabled = false;
    this.textContent = '🤖 Rédiger avec l\'IA';
});
</script>
@endsection
