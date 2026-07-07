<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ app()->getLocale() === 'en' ? 'Page Not Found — WhatsAppBizAI' : 'Page introuvable — WhatsAppBizAI' }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #0f172a; color: #fff; min-height: 100vh; display: flex; align-items: center; justify-content: center; text-align: center; padding: 24px; }
        h1 { font-size: 100px; font-weight: 900; color: #0ea5e9; line-height: 1; }
        h2 { font-size: 24px; font-weight: 700; margin: 16px 0 10px; }
        p  { color: #94a3b8; font-size: 15px; }
        a  { display: inline-block; margin-top: 28px; background: #0ea5e9; color: #fff; padding: 12px 28px; border-radius: 8px; font-weight: 700; text-decoration: none; font-size: 15px; }
    </style>
</head>
<body>
    <div>
        <h1>404</h1>
        <h2>{{ app()->getLocale() === 'en' ? 'Page Not Found' : 'Page introuvable' }}</h2>
        <p>{{ app()->getLocale() === 'en' ? 'This page does not exist or has been moved.' : 'Cette page n\'existe pas ou a été déplacée.' }}</p>
        <a href="{{ url('/') }}">{{ app()->getLocale() === 'en' ? '← Back to home' : '← Retour à l\'accueil' }}</a>
    </div>
</body>
</html>
