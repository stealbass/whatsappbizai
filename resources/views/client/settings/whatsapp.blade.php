@extends('client.layout')
@section('title', 'WhatsApp & IA')

@section('content')
<div class="card" style="max-width:600px;">
    <div class="card-header"><h2>Configuration WhatsApp & Intelligence Artificielle</h2></div>

    @if($errors->any())
        <div class="alert alert-error">
            @foreach($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
        </div>
    @endif

    <form action="{{ url('client/settings/whatsapp') }}" method="POST">
        @csrf @method('PUT')
        <div class="form-group">
            <label>WhatsApp Phone Number ID *</label>
            <input type="text" name="whatsapp_phone_number_id" value="{{ old('whatsapp_phone_number_id', $business->whatsapp_phone_number_id ?? '') }}" required>
            <p class="form-help">Identifiant du numéro de téléphone WhatsApp Business.</p>
        </div>
        <div class="form-group">
            <label>WhatsApp Access Token *</label>
            <input type="password" name="whatsapp_access_token" value="{{ old('whatsapp_access_token', $business->whatsapp_access_token ?? '') }}" required>
            <p class="form-help">Jeton d'accès temporaire ou permanent de l'API Cloud.</p>
        </div>
        <div class="form-group">
            <label>WhatsApp Business Account ID *</label>
            <input type="text" name="whatsapp_business_account_id" value="{{ old('whatsapp_business_account_id', $business->whatsapp_business_account_id ?? '') }}" required>
        </div>
        <div class="form-group">
            <label>System Prompt IA (Gemini)</label>
            <textarea name="gemini_system_prompt" rows="6" placeholder="Vous êtes un assistant IA pour...">{{ old('gemini_system_prompt', $business->gemini_system_prompt ?? '') }}</textarea>
            <p class="form-help">Instructions données à l'IA pour guider ses réponses aux clients.</p>
        </div>
        <div style="display:flex;gap:12px;margin-top:20px;">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
    </form>
</div>
@endsection
