<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('app.register.title') }} — {{ $site->site_name ?? 'WhatsAppBizAI' }}</title>
    <link rel="icon" type="image/x-icon" href="{{ $site->favicon_path ? asset('storage/' . $site->favicon_path) : asset('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/switchers.css') }}">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #0f172a; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .card { background: #fff; border-radius: 16px; padding: 40px; width: 100%; max-width: 480px; box-shadow: 0 25px 50px rgba(0,0,0,.4); }
        .logo { text-align: center; margin-bottom: 32px; }
        .logo h1 { font-size: 26px; font-weight: 800; color: #0f172a; }
        .logo span { color: #0ea5e9; }
        .logo p { color: #64748b; font-size: 14px; margin-top: 6px; }
        .switcher-row { display: flex; justify-content: center; margin-bottom: 24px; }
        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        input { width: 100%; padding: 11px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; outline: none; transition: border .2s; }
        input:focus { border-color: #0ea5e9; box-shadow: 0 0 0 3px rgba(14,165,233,.1); }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .btn { width: 100%; background: #0ea5e9; color: #fff; border: none; padding: 13px; border-radius: 8px; font-size: 15px; font-weight: 700; cursor: pointer; margin-top: 8px; transition: background .2s; }
        .btn:hover { background: #0284c7; }
        .login-link { text-align: center; margin-top: 20px; font-size: 13px; color: #64748b; }
        .login-link a { color: #0ea5e9; text-decoration: none; font-weight: 600; }
        .error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px; border-radius: 8px; font-size: 13px; margin-bottom: 18px; }
        .features { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 28px; justify-content: center; }
        .badge { background: #f0f9ff; color: #0369a1; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .divider { border: none; border-top: 1px solid #e5e7eb; margin: 24px 0; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">
            @php $siteName = $site->site_name ?? 'WhatsAppBizAI'; $parts = explode('BizAI', $siteName); @endphp
            <h1>{!! $parts[0] ?? $siteName !!}<span>{{ str_contains($siteName, 'BizAI') ? 'BizAI' : '' }}</span></h1>
            <p>{{ __('app.register.subtitle') }}</p>
        </div>

        <div class="switcher-row">
            <div class="switcher-wrap" style="border:1px solid #e5e7eb;border-radius:8px;padding:4px 8px;">
                <button class="switcher-btn lang-btn" data-lang="fr" style="border:none;">FR</button>
                <button class="switcher-btn lang-btn" data-lang="en" style="border:none;">EN</button>
            </div>
        </div>

        <div class="features">
            <span class="badge">{{ __('app.register.badge_ai') }}</span>
            <span class="badge">{{ __('app.register.badge_quotes') }}</span>
            <span class="badge">{{ __('app.register.badge_invoices') }}</span>
            <span class="badge">{{ __('app.register.badge_reminders') }}</span>
        </div>

        @if($errors->any())
        <div class="error">
            @foreach($errors->all() as $error)
                <div>• {{ $error }}</div>
            @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}">
            @csrf

            <div class="form-group">
                <label>{{ __('app.register.business_name') }}</label>
                <input type="text" name="business_name" value="{{ old('business_name') }}"
                    placeholder="{{ __('app.register.business_placeholder') }}" required>
            </div>

            <div class="row">
                <div class="form-group">
                    <label>{{ __('app.register.owner_name') }}</label>
                    <input type="text" name="owner_name" value="{{ old('owner_name') }}"
                        placeholder="{{ __('app.register.owner_placeholder') }}" required>
                </div>
                <div class="form-group">
                    <label>{{ __('app.register.city') }}</label>
                    <input type="text" name="city" value="{{ old('city', 'Douala') }}"
                        placeholder="{{ __('app.register.city_placeholder') }}" required>
                </div>
            </div>

            <div class="form-group">
                <label>{{ __('app.register.email') }}</label>
                <input type="email" name="email" value="{{ old('email') }}"
                    placeholder="{{ __('app.register.email_placeholder') }}" required>
            </div>

            <div class="row">
                <div class="form-group">
                    <label>{{ __('app.register.password') }}</label>
                    <input type="password" name="password" placeholder="{{ __('app.register.password_placeholder') }}" required>
                </div>
                <div class="form-group">
                    <label>{{ __('app.register.password_confirm') }}</label>
                    <input type="password" name="password_confirmation" placeholder="{{ __('app.register.password_confirm_placeholder') }}" required>
                </div>
            </div>

            <button type="submit" class="btn">{{ __('app.register.submit') }}</button>
        </form>

        <hr class="divider">

        <div class="login-link">
            <span>{{ __('app.register.has_account') }}</span> <a href="{{ url('login') }}">{{ __('app.register.login') }}</a>
        </div>
    </div>

<script>
window.__i18n = {
    fr: {!! json_encode([
        'register' => trans('app.register', [], 'fr'),
    ]) !!},
    en: {!! json_encode([
        'register' => trans('app.register', [], 'en'),
    ]) !!}
};
</script>
<script src="{{ asset('js/preferences.js') }}?v={{ time() }}"></script>
</body>
</html>
