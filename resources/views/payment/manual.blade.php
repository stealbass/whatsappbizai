<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement manuel — WhatsAppBizAI</title>
    <meta name="robots" content="noindex">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:#0f172a;color:#fff;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px}
        .card{background:#1e293b;border-radius:16px;padding:40px;width:100%;max-width:560px}
        h1{font-size:22px;font-weight:800;margin-bottom:6px}
        .subtitle{color:#94a3b8;font-size:14px;margin-bottom:28px}
        label{display:block;font-size:13px;font-weight:600;margin-bottom:6px;color:#cbd5e1}
        input,select,textarea{width:100%;padding:11px 14px;background:#0f172a;border:1px solid #334155;border-radius:8px;color:#fff;font-size:14px;margin-bottom:18px;outline:none}
        input:focus,select:focus,textarea:focus{border-color:#0ea5e9}
        select option{background:#1e293b}
        .coords{background:#0f172a;border:1px solid #334155;border-radius:10px;padding:16px;margin-bottom:20px;font-size:13px;line-height:1.8;color:#94a3b8}
        .coords strong{color:#fff}
        .coords .method-block{display:none}
        .coords .method-block.active{display:block}
        .btn{width:100%;padding:14px;background:#0ea5e9;color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer}
        .btn:hover{opacity:.9}
        .alert-success{background:#166534;border:1px solid #16a34a;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:14px}
        .alert-error{background:#7f1d1d;border:1px solid #ef4444;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:14px}
        a.back{display:inline-block;margin-top:16px;color:#0ea5e9;font-size:13px;text-decoration:none}
        @media(max-width:600px){.card{padding:24px}}
    </style>
</head>
<body>
<div class="card">
    <h1>💳 Paiement manuel</h1>
    <p class="subtitle">Payez via Mobile Money, Orange Money, Wave ou virement bancaire puis soumettez votre preuve.</p>

    @if(session('success'))
        <div class="alert-success">✅ {{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert-error">❌ {{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('payment.manual.store') }}" enctype="multipart/form-data">
        @csrf

        <label>Plan souhaité</label>
        <select name="plan" required onchange="updateAmount()">
            <option value="starter" {{ request('plan') === 'starter' ? 'selected' : '' }}>Starter — 9 900 XAF/mois</option>
            <option value="business" {{ request('plan') === 'business' ? 'selected' : '' }}>Business — 24 900 XAF/mois</option>
            <option value="pro" {{ request('plan') === 'pro' ? 'selected' : '' }}>Pro — 49 900 XAF/mois</option>
        </select>

        <label>Cycle de facturation</label>
        <select name="cycle" required>
            <option value="monthly">Mensuel</option>
            <option value="yearly">Annuel (2 mois offerts)</option>
        </select>

        <label>Méthode de paiement</label>
        <select name="method" required onchange="showCoords(this.value)">
            <option value="">-- Choisir --</option>
            <option value="mtn_momo">MTN Mobile Money</option>
            <option value="orange_money">Orange Money</option>
            <option value="wave">Wave</option>
            <option value="bank_transfer">Virement bancaire</option>
            <option value="other">Autre</option>
        </select>

        <div class="coords" id="coords-box" style="display:none">
            <div class="method-block" id="coord-mtn_momo">
                <strong>MTN MoMo — Numéro de réception :</strong><br>
                📱 <strong>+237 6XX XXX XXX</strong><br>
                Nom : <strong>WhatsAppBizAI SARL</strong>
            </div>
            <div class="method-block" id="coord-orange_money">
                <strong>Orange Money — Numéro de réception :</strong><br>
                📱 <strong>+237 6XX XXX XXX</strong><br>
                Nom : <strong>WhatsAppBizAI SARL</strong>
            </div>
            <div class="method-block" id="coord-wave">
                <strong>Wave — Numéro :</strong><br>
                📱 <strong>+221 7X XXX XXXX</strong><br>
                Nom : <strong>WhatsAppBizAI</strong>
            </div>
            <div class="method-block" id="coord-bank_transfer">
                <strong>Virement bancaire :</strong><br>
                Banque : <strong>Société Générale Cameroun</strong><br>
                IBAN/Compte : <strong>XXXXXXXXXXXXXXXX</strong><br>
                Motif : <strong>Abonnement WhatsAppBizAI + votre email</strong>
            </div>
        </div>

        <label>Numéro ayant effectué le paiement</label>
        <input type="text" name="phone_number" placeholder="+237 6XX XXX XXX" value="{{ old('phone_number') }}">

        <label>Référence / ID de la transaction *</label>
        <input type="text" name="reference" required placeholder="Ex: TXN123456789" value="{{ old('reference') }}">

        <label>Capture d'écran de la confirmation (optionnel)</label>
        <input type="file" name="screenshot" accept="image/*">

        <label>Notes complémentaires</label>
        <textarea name="notes" rows="2" placeholder="Toute information utile...">{{ old('notes') }}</textarea>

        <button type="submit" class="btn">📤 Soumettre ma preuve de paiement</button>
    </form>

    <a class="back" href="{{ route('payment.pricing') }}">← Retour aux tarifs</a>
</div>

<script>
function showCoords(method) {
    const box = document.getElementById('coords-box');
    document.querySelectorAll('.method-block').forEach(el => el.classList.remove('active'));
    if (method && document.getElementById('coord-' + method)) {
        box.style.display = 'block';
        document.getElementById('coord-' + method).classList.add('active');
    } else {
        box.style.display = 'none';
    }
}
</script>
</body>
</html>
