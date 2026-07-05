@extends('client.layout')
@section('title', 'Mon entreprise')

@section('content')
<div class="card" style="max-width:600px;">
    <div class="card-header"><h2>Paramètres de l'entreprise</h2></div>

    @if($errors->any())
        <div class="alert alert-error">
            @foreach($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
        </div>
    @endif

    <form action="{{ url('client/settings/business') }}" method="POST">
        @csrf @method('PUT')
        <div class="form-group">
            <label>Nom de l'entreprise *</label>
            <input type="text" name="name" value="{{ old('name', $business->name ?? '') }}" required>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Dirigeant</label>
                <input type="text" name="owner_name" value="{{ old('owner_name', $business->owner_name ?? '') }}">
            </div>
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" value="{{ old('email', $business->email ?? '') }}" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Téléphone</label>
                <input type="text" name="phone" value="{{ old('phone', $business->phone ?? '') }}">
            </div>
            <div class="form-group">
                <label>Ville</label>
                <input type="text" name="city" value="{{ old('city', $business->city ?? '') }}">
            </div>
        </div>
        <div class="form-group">
            <label>Adresse</label>
            <input type="text" name="address" value="{{ old('address', $business->address ?? '') }}">
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Pays</label>
                <input type="text" name="country" value="{{ old('country', $business->country ?? '') }}">
            </div>
            <div class="form-group">
                <label>Devise</label>
                <select name="currency">
                    <option value="XAF" {{ old('currency', $business->currency ?? 'XAF') === 'XAF' ? 'selected' : '' }}>XAF — Franc CFA</option>
                    <option value="USD" {{ old('currency', $business->currency ?? '') === 'USD' ? 'selected' : '' }}>USD — Dollar US</option>
                    <option value="EUR" {{ old('currency', $business->currency ?? '') === 'EUR' ? 'selected' : '' }}>EUR — Euro</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>Fuseau horaire</label>
            <select name="timezone">
                @foreach(['Africa/Douala','Africa/Lagos','Africa/Abidjan','Europe/Paris','Europe/London','America/New_York','UTC'] as $tz)
                    <option value="{{ $tz }}" {{ old('timezone', $business->timezone ?? 'Africa/Douala') === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Préfixe facture</label>
                <input type="text" name="invoice_prefix" value="{{ old('invoice_prefix', $business->invoice_prefix ?? 'FAC-') }}">
            </div>
            <div class="form-group">
                <label>Préfixe devis</label>
                <input type="text" name="quote_prefix" value="{{ old('quote_prefix', $business->quote_prefix ?? 'DEV-') }}">
            </div>
        </div>
        <div style="display:flex;gap:12px;margin-top:20px;">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
    </form>
</div>
@endsection
