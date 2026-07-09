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

    <form action="{{ url('client/settings/business') }}" method="POST" enctype="multipart/form-data">
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
                <select name="country" id="country-select">
                    @php
                        $countries = \App\Helpers\Country::options();
                        $currentCountry = old('country', $business->country ?? 'CM');
                    @endphp
                    @foreach($countries as $code => $name)
                        <option value="{{ $code }}" {{ $currentCountry === $code ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>{{ __('app.client.settings.business.currency') }}</label>
                <select name="currency">
                    @php
                        $currencies = [
                            'XAF' => 'XAF — FCFA BEAC',
                            'XOF' => 'XOF — FCFA BCEAO',
                            'USD' => 'USD — Dollar US',
                            'EUR' => 'EUR — Euro',
                            'GBP' => 'GBP — Livre Sterling',
                            'ZAR' => 'ZAR — Rand Sud-Africain',
                            'NGN' => 'NGN — Naira',
                            'GHS' => 'GHS — Cedi',
                            'KES' => 'KES — Shilling',
                            'MAD' => 'MAD — Dirham',
                            'GNF' => 'GNF — Franc Guinéen',
                            'EGP' => 'EGP — Livre Égyptienne',
                            'TND' => 'TND — Dinar Tunisien',
                            'DZD' => 'DZD — Dinar Algérien',
                            'TZS' => 'TZS — Shilling Tanzanien',
                            'UGX' => 'UGX — Shilling Ougandais',
                            'RWF' => 'RWF — Franc Rwandais',
                            'ETB' => 'ETB — Birr Éthiopien',
                            'MGA' => 'MGA — Ariary Malgache',
                            'MUR' => 'MUR — Rupée Mauricienne',
                            'CAD' => 'CAD — Dollar Canadien',
                            'AED' => 'AED — Dirham Émirati',
                            'CNY' => 'CNY — Yuan Chinois',
                            'INR' => 'INR — Rupée Indienne',
                            'TRY' => 'TRY — Lire Turque',
                            'AOA' => 'AOA — Kwanza Angolais',
                            'ZMW' => 'ZMW — Kwacha Zambien',
                            'MWK' => 'MWK — Kwacha Malawite',
                            'BWP' => 'BWP — Pula Botswanais',
                            'SZL' => 'SZL — Lilangeni Swazi',
                        ];
                        $currentCurrency = old('currency', $business->currency ?? 'XAF');
                    @endphp
                    @foreach($currencies as $code => $label)
                        <option value="{{ $code }}" {{ $currentCurrency === $code ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>{{ __('app.client.settings.business.timezone') }}</label>
            <select name="timezone">
                @php
                    $timezones = [
                        'Africa/Douala' => 'Douala (WAT)',
                        'Africa/Brazzaville' => 'Brazzaville (WAT)',
                        'Africa/Kinshasa' => 'Kinshasa (WAT)',
                        'Africa/Lubumbashi' => 'Lubumbashi (CAT)',
                        'Africa/Libreville' => 'Libreville (WAT)',
                        'Africa/Malabo' => 'Malabo (WAT)',
                        'Africa/Bangui' => 'Bangui (WAT)',
                        'Africa/Ndjamena' => 'N\'Djamena (WAT)',
                        'Africa/Dakar' => 'Dakar (GMT)',
                        'Africa/Abidjan' => 'Abidjan (GMT)',
                        'Africa/Bamako' => 'Bamako (GMT)',
                        'Africa/Ouagadougou' => 'Ouagadougou (GMT)',
                        'Africa/Niamey' => 'Niamey (GMT)',
                        'Africa/Lome' => 'Lomé (GMT)',
                        'Africa/Porto-Novo' => 'Porto-Novo (WAT)',
                        'Africa/Conakry' => 'Conakry (GMT)',
                        'Africa/Lagos' => 'Lagos (WAT)',
                        'Africa/Accra' => 'Accra (GMT)',
                        'Africa/Casablanca' => 'Casablanca (WET)',
                        'Africa/Tunis' => 'Tunis (CET)',
                        'Africa/Algiers' => 'Alger (CET)',
                        'Africa/Cairo' => 'Le Caire (EET)',
                        'Africa/Nairobi' => 'Nairobi (EAT)',
                        'Africa/Dar_es_Salaam' => 'Dar es Salaam (EAT)',
                        'Africa/Kampala' => 'Kampala (EAT)',
                        'Africa/Kigali' => 'Kigali (CAT)',
                        'Africa/Addis_Ababa' => 'Addis-Abeba (EAT)',
                        'Africa/Johannesburg' => 'Johannesburg (SAST)',
                        'Africa/Maputo' => 'Maputo (CAT)',
                        'Africa/Lusaka' => 'Lusaka (CAT)',
                        'Africa/Harare' => 'Harare (CAT)',
                        'Africa/Blantyre' => 'Blantyre (CAT)',
                        'Africa/Luanda' => 'Luanda (WAT)',
                        'Indian/Antananarivo' => 'Antananarivo (EAT)',
                        'Indian/Mauritius' => 'Port Louis (MUT)',
                        'Europe/Paris' => 'Paris (CET)',
                        'Europe/Brussels' => 'Bruxelles (CET)',
                        'Europe/London' => 'Londres (GMT)',
                        'America/New_York' => 'New York (EST)',
                        'America/Chicago' => 'Chicago (CST)',
                        'America/Los_Angeles' => 'Los Angeles (PST)',
                        'America/Toronto' => 'Toronto (EST)',
                        'America/Vancouver' => 'Vancouver (PST)',
                        'Asia/Dubai' => 'Dubaï (GST)',
                        'Asia/Shanghai' => 'Shanghai (CST)',
                        'Asia/Kolkata' => 'Kolkata (IST)',
                        'Europe/Istanbul' => 'Istanbul (TRT)',
                        'UTC' => 'UTC',
                    ];
                    $currentTz = old('timezone', $business->timezone ?? 'Africa/Douala');
                @endphp
                @foreach($timezones as $tz => $label)
                    <option value="{{ $tz }}" {{ $currentTz === $tz ? 'selected' : '' }}>{{ $label }}</option>
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
        <div class="form-group">
            <label>Logo de l'entreprise</label>
            @if(!empty($business->logo_path) && \Storage::disk('public')->exists($business->logo_path))
                <div style="margin-bottom:8px;">
                    <img src="{{ asset('storage/' . $business->logo_path) }}" alt="Logo" style="max-height:60px;border-radius:6px;">
                </div>
            @endif
            <input type="file" name="logo" accept="image/*">
            <small style="color:var(--gray);">PNG, JPG, SVG — max 2 Mo</small>
        </div>
        <div style="display:flex;gap:12px;margin-top:20px;">
            <button type="submit" class="btn btn-primary">{{ __('app.client.settings.business.save') }}</button>
        </div>
    </form>
</div>

<script>
(function(){
    const countryTimezones = @json(collect(\App\Helpers\Country::options())->mapWithKeys(function($name, $code) {
        return [$code => \App\Helpers\Country::timezones($code)];
    })->all());
    const countryCurrencies = @json(collect(\App\Helpers\Country::options())->mapWithKeys(function($name, $code) {
        return [$code => \App\Helpers\Country::defaultCurrency($code)];
    })->all());

    const countrySelect = document.getElementById('country-select');
    if (countrySelect) {
        countrySelect.addEventListener('change', function() {
            const code = this.value;

            // Update timezone
            const tzSelect = document.querySelector('select[name="timezone"]');
            if (tzSelect && countryTimezones[code]) {
                const timezones = countryTimezones[code];
                const currentVal = tzSelect.value;
                // Check if current timezone is still valid for new country
                if (!timezones.includes(currentVal)) {
                    tzSelect.value = timezones[0];
                }
            }

            // Update currency
            const curSelect = document.querySelector('select[name="currency"]');
            if (curSelect && countryCurrencies[code]) {
                curSelect.value = countryCurrencies[code];
            }
        });
    }
})();
</script>
@endsection
