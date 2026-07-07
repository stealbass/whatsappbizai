<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('app.payment.manual_title') }} — WhatsAppBizAI</title>
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
    <h1>{{ __('app.payment.manual_title') }}</h1>
    <p class="subtitle">{{ __('app.payment.manual_desc') }}</p>

    @if(session('success'))
        <div class="alert-success">✅ {{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert-error">❌ {{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('payment.manual.store') }}" enctype="multipart/form-data">
        @csrf

        <label>{{ __('app.payment.desired_plan') }}</label>
        <select name="plan" required onchange="updateAmount()">
            <option value="starter" {{ request('plan') === 'starter' ? 'selected' : '' }}>Starter — 9 900 XAF/{{ __('app.payment.monthly') }}</option>
            <option value="business" {{ request('plan') === 'business' ? 'selected' : '' }}>Business — 24 900 XAF/{{ __('app.payment.monthly') }}</option>
            <option value="pro" {{ request('plan') === 'pro' ? 'selected' : '' }}>Pro — 49 900 XAF/{{ __('app.payment.monthly') }}</option>
        </select>

        <label>{{ __('app.payment.billing_cycle') }}</label>
        <select name="cycle" required>
            <option value="monthly">{{ __('app.payment.monthly') }}</option>
            <option value="yearly">{{ __('app.payment.yearly') }}</option>
        </select>

        <label>{{ __('app.payment.payment_method') }}</label>
        <select name="method" required onchange="showCoords(this.value)">
            <option value="">{{ __('app.payment.select_method') }}</option>
            <option value="mtn_momo">MTN Mobile Money</option>
            <option value="orange_money">Orange Money</option>
            <option value="wave">Wave</option>
            <option value="bank_transfer">{{ __('app.payment.bank_transfer') }}</option>
            <option value="other">{{ __('app.payment.other') }}</option>
        </select>

        <div class="coords" id="coords-box" style="display:none">
            <div class="method-block" id="coord-mtn_momo">
                <strong>MTN MoMo — {{ __('app.payment.receiving_number') }} :</strong><br>
                📱 <strong>+237 6XX XXX XXX</strong><br>
                {{ __('app.payment.name') }} : <strong>WhatsAppBizAI SARL</strong>
            </div>
            <div class="method-block" id="coord-orange_money">
                <strong>Orange Money — {{ __('app.payment.receiving_number') }} :</strong><br>
                📱 <strong>+237 6XX XXX XXX</strong><br>
                {{ __('app.payment.name') }} : <strong>WhatsAppBizAI SARL</strong>
            </div>
            <div class="method-block" id="coord-wave">
                <strong>Wave — {{ __('app.payment.number') }} :</strong><br>
                📱 <strong>+221 7X XXX XXXX</strong><br>
                {{ __('app.payment.name') }} : <strong>WhatsAppBizAI</strong>
            </div>
            <div class="method-block" id="coord-bank_transfer">
                <strong>{{ __('app.payment.bank_transfer') }} :</strong><br>
                {{ __('app.payment.bank') }} : <strong>Société Générale Cameroun</strong><br>
                {{ __('app.payment.iban') }} : <strong>XXXXXXXXXXXXXXXX</strong><br>
                {{ __('app.payment.reference') }} : <strong>{{ __('app.payment.reference_hint') }}</strong>
            </div>
        </div>

        <label>{{ __('app.payment.payer_number') }}</label>
        <input type="text" name="phone_number" placeholder="+237 6XX XXX XXX" value="{{ old('phone_number') }}">

        <label>{{ __('app.payment.transaction_id') }} *</label>
        <input type="text" name="reference" required placeholder="{{ __('app.payment.transaction_placeholder') }}" value="{{ old('reference') }}">

        <label>{{ __('app.payment.proof_screenshot') }}</label>
        <input type="file" name="screenshot" accept="image/*">

        <label>{{ __('app.payment.additional_notes') }}</label>
        <textarea name="notes" rows="2" placeholder="{{ __('app.payment.notes_placeholder') }}">{{ old('notes') }}</textarea>

        <button type="submit" class="btn">{{ __('app.payment.submit_proof') }}</button>
    </form>

    <a class="back" href="{{ route('payment.pricing') }}">{{ __('app.payment.back_pricing') }}</a>
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
