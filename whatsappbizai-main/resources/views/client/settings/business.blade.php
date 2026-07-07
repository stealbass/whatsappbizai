@extends('client.layout')
@section('title', __('app.client.settings.business.title'))

@section('content')
<div class="card" style="max-width:600px;">
    <div class="card-header"><h2>{{ __('app.client.settings.business.title') }}</h2></div>

    @if($errors->any())
        <div class="alert alert-error">
            @foreach($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
        </div>
    @endif

    <form action="{{ url('client/settings/business') }}" method="POST">
        @csrf @method('PUT')
        <div class="form-group">
            <label>{{ __('app.client.settings.business.name') }} *</label>
            <input type="text" name="name" value="{{ old('name', $business->name ?? '') }}" required>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>{{ __('app.client.settings.business.owner') }}</label>
                <input type="text" name="owner_name" value="{{ old('owner_name', $business->owner_name ?? '') }}">
            </div>
            <div class="form-group">
                <label>{{ __('app.client.settings.business.email') }} *</label>
                <input type="email" name="email" value="{{ old('email', $business->email ?? '') }}" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>{{ __('app.client.settings.business.phone') }}</label>
                <input type="text" name="phone" value="{{ old('phone', $business->phone ?? '') }}">
            </div>
            <div class="form-group">
                <label>{{ __('app.client.settings.business.city') }}</label>
                <input type="text" name="city" value="{{ old('city', $business->city ?? '') }}">
            </div>
        </div>
        <div class="form-group">
            <label>{{ __('app.client.settings.business.address') }}</label>
            <input type="text" name="address" value="{{ old('address', $business->address ?? '') }}">
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>{{ __('app.client.settings.business.country') }}</label>
                <input type="text" name="country" value="{{ old('country', $business->country ?? '') }}">
            </div>
            <div class="form-group">
                <label>{{ __('app.client.settings.business.currency') }}</label>
                <select name="currency">
                    <option value="XAF" {{ old('currency', $business->currency ?? 'XAF') === 'XAF' ? 'selected' : '' }}>XAF — FCFA BEAC</option>
                    <option value="XOF" {{ old('currency', $business->currency ?? '') === 'XOF' ? 'selected' : '' }}>XOF — FCFA BCEAO</option>
                    <option value="USD" {{ old('currency', $business->currency ?? '') === 'USD' ? 'selected' : '' }}>USD — Dollar US</option>
                    <option value="EUR" {{ old('currency', $business->currency ?? '') === 'EUR' ? 'selected' : '' }}>EUR — Euro</option>
                    <option value="GBP" {{ old('currency', $business->currency ?? '') === 'GBP' ? 'selected' : '' }}>GBP — Livre Sterling</option>
                    <option value="ZAR" {{ old('currency', $business->currency ?? '') === 'ZAR' ? 'selected' : '' }}>ZAR — Rand Sud-Africain</option>
                    <option value="NGN" {{ old('currency', $business->currency ?? '') === 'NGN' ? 'selected' : '' }}>NGN — Naira</option>
                    <option value="GHS" {{ old('currency', $business->currency ?? '') === 'GHS' ? 'selected' : '' }}>GHS — Cedi</option>
                    <option value="KES" {{ old('currency', $business->currency ?? '') === 'KES' ? 'selected' : '' }}>KES — Shilling</option>
                    <option value="MAD" {{ old('currency', $business->currency ?? '') === 'MAD' ? 'selected' : '' }}>MAD — Dirham</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>{{ __('app.client.settings.business.timezone') }}</label>
            <select name="timezone">
                @foreach(['Africa/Douala','Africa/Lagos','Africa/Abidjan','Europe/Paris','Europe/London','America/New_York','UTC'] as $tz)
                    <option value="{{ $tz }}" {{ old('timezone', $business->timezone ?? 'Africa/Douala') === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>{{ __('app.client.settings.business.invoice_prefix') }}</label>
                <input type="text" name="invoice_prefix" value="{{ old('invoice_prefix', $business->invoice_prefix ?? 'FAC-') }}">
            </div>
            <div class="form-group">
                <label>{{ __('app.client.settings.business.quote_prefix') }}</label>
                <input type="text" name="quote_prefix" value="{{ old('quote_prefix', $business->quote_prefix ?? 'DEV-') }}">
            </div>
        </div>
        <div style="display:flex;gap:12px;margin-top:20px;">
            <button type="submit" class="btn btn-primary">{{ __('app.client.settings.business.save') }}</button>
        </div>
    </form>
</div>
@endsection
