<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — WhatsAppBizAI</title>
    <link rel="stylesheet" href="{{ asset('css/switchers.css') }}">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #0f172a; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .card { background: #fff; border-radius: 16px; padding: 40px; width: 100%; max-width: 420px; box-shadow: 0 25px 50px rgba(0,0,0,.4); }
        .logo { text-align: center; margin-bottom: 32px; }
        .logo h1 { font-size: 26px; font-weight: 800; color: #0f172a; }
        .logo span { color: #0ea5e9; }
        .logo p { color: #64748b; font-size: 14px; margin-top: 6px; }
        .switcher-row { display: flex; justify-content: center; margin-bottom: 24px; }
        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        input[type="email"], input[type="password"] { width: 100%; padding: 11px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; outline: none; transition: border .2s; }
        input:focus { border-color: #0ea5e9; box-shadow: 0 0 0 3px rgba(14,165,233,.1); }
        .remember-row { display: flex; align-items: center; gap: 8px; margin-bottom: 18px; }
        .remember-row input[type="checkbox"] { width: auto; }
        .remember-row label { margin: 0; font-size: 13px; color: #64748b; }
        .btn { width: 100%; background: #0ea5e9; color: #fff; border: none; padding: 13px; border-radius: 8px; font-size: 15px; font-weight: 700; cursor: pointer; transition: background .2s; }
        .btn:hover { background: #0284c7; }
        .register-link { text-align: center; margin-top: 20px; font-size: 13px; color: #64748b; }
        .register-link a { color: #0ea5e9; text-decoration: none; font-weight: 600; }
        .error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px; border-radius: 8px; font-size: 13px; margin-bottom: 18px; }
        .divider { border: none; border-top: 1px solid #e5e7eb; margin: 24px 0; }
        .admin-link { text-align: center; margin-top: 12px; font-size: 12px; color: #94a3b8; }
        .admin-link a { color: #94a3b8; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">
            <h1>WhatsApp<span>BizAI</span></h1>
            <p>{{ app()->getLocale() === 'fr' ? 'Connectez-vous à votre espace' : 'Log in to your account' }}</p>
        </div>

        <div class="switcher-row">
            <div class="switcher-wrap" style="border:1px solid #e5e7eb;border-radius:8px;padding:4px 8px;">
                <button class="switcher-btn lang-btn" data-lang="fr" style="border:none;">FR</button>
                <button class="switcher-btn lang-btn" data-lang="en" style="border:none;">EN</button>
            </div>
        </div>

        @if($errors->any())
        <div class="error">
            @foreach($errors->all() as $error)
                <div>• {{ $error }}</div>
            @endforeach
        </div>
        @endif

        <form method="POST" action="{{ url('login') }}">
            @csrf

            <div class="form-group">
                <label>{{ app()->getLocale() === 'fr' ? 'Adresse email' : 'Email address' }}</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="form-group">
                <label>{{ app()->getLocale() === 'fr' ? 'Mot de passe' : 'Password' }}</label>
                <input type="password" name="password" required>
            </div>

            <div class="remember-row">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">{{ app()->getLocale() === 'fr' ? 'Se souvenir de moi' : 'Remember me' }}</label>
            </div>

            <button type="submit" class="btn">{{ app()->getLocale() === 'fr' ? 'Se connecter' : 'Log in' }}</button>
        </form>

        <div style="position:relative;text-align:center;margin:20px 0;">
            <hr class="divider">
            <span style="position:absolute;top:-8px;left:50%;transform:translateX(-50%);background:#fff;padding:0 10px;font-size:12px;color:#94a3b8;">OU</span>
        </div>

        <a href="{{ url('auth/google') }}" style="display:flex;align-items:center;justify-content:center;gap:10px;width:100%;padding:12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;font-weight:600;color:#374151;text-decoration:none;background:#fff;transition:background .2s;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='#fff'">
            <svg width="20" height="20" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
            {{ app()->getLocale() === 'fr' ? 'Continuer avec Google' : 'Continue with Google' }}
        </a>

        <div class="register-link">
            {{ app()->getLocale() === 'fr' ? 'Pas encore de compte ?' : "Don't have an account?" }} <a href="{{ url('register') }}">{{ app()->getLocale() === 'fr' ? 'Créer un compte' : 'Sign up' }}</a>
        </div>

        <hr class="divider">

        <div class="admin-link">
            <a href="/admin/login">{{ app()->getLocale() === 'fr' ? 'Espace administrateur' : 'Admin panel' }}</a>
        </div>
    </div>
</body>
</html>
