<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ app()->getLocale() === 'en' ? 'Session Expired — WhatsAppBizAI' : 'Session expirée — WhatsAppBizAI' }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #0f172a; color: #fff; min-height: 100vh; display: flex; align-items: center; justify-content: center; text-align: center; padding: 24px; }
        h1 { font-size: 80px; font-weight: 900; color: #f59e0b; line-height: 1; }
        h2 { font-size: 24px; font-weight: 700; margin: 16px 0 10px; }
        p  { color: #94a3b8; font-size: 15px; }
        a  { display: inline-block; margin-top: 28px; background: #0ea5e9; color: #fff; padding: 12px 28px; border-radius: 8px; font-weight: 700; text-decoration: none; }
    </style>
</head>
<body>
    <div>
        <h1>419</h1>
        <h2>{{ app()->getLocale() === 'en' ? 'Session Expired' : 'Session expirée' }}</h2>
        <p>{{ app()->getLocale() === 'en' ? 'Your session has expired. Please refresh the page and try again.' : 'Votre session a expiré. Veuillez rafraîchir la page et réessayer.' }}</p>
        <a href="javascript:history.back()">{{ app()->getLocale() === 'en' ? '← Back' : '← Retour' }}</a>
    </div>
</body>
</html>
