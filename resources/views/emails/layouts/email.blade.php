<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'WhatsAppBizAI' }}</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:32px 16px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08);">

                    {{-- Header --}}
                    <tr>
                        <td style="background:linear-gradient(135deg,#0ea5e9,#0284c7);padding:28px 32px;text-align:center;">
                            <h1 style="margin:0;color:#ffffff;font-size:22px;font-weight:800;">
                                🟢 {{ $siteName ?? 'WhatsAppBizAI' }}
                            </h1>
                        </td>
                    </tr>

                    {{-- Content --}}
                    <tr>
                        <td style="padding:32px;">
                            @yield('content')
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background:#f8fafc;padding:20px 32px;border-top:1px solid #e2e8f0;text-align:center;">
                            <p style="margin:0;font-size:12px;color:#94a3b8;">
                                © {{ date('Y') }} {{ $siteName ?? 'WhatsAppBizAI' }} — Tous droits réservés
                            </p>
                            <p style="margin:6px 0 0;font-size:11px;color:#cbd5e1;">
                                <a href="{{ url('/') }}" style="color:#0ea5e9;text-decoration:none;">Site web</a> ·
                                <a href="{{ url('login') }}" style="color:#0ea5e9;text-decoration:none;">Connexion</a> ·
                                <a href="{{ url('privacy') }}" style="color:#0ea5e9;text-decoration:none;">Confidentialité</a>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
