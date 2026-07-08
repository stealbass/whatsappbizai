@component('email.layouts.email')

@slot('content')
<h2 style="margin:0 0 16px;font-size:20px;font-weight:700;color:#0f172a;">
    👋 Bienvenue {{ $userName }} !
</h2>

<p style="margin:0 0 16px;font-size:15px;color:#475569;line-height:1.7;">
    Votre compte <strong>{{ $businessName }}</strong> a été créé avec succès.
    Vous pouvez dès maintenant configurer votre espace et commencer à automatiser votre business sur WhatsApp.
</p>

<h3 style="margin:24px 0 12px;font-size:16px;font-weight:700;color:#0f172a;">🚀 Prochaines étapes</h3>

<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:16px;">
    <tr>
        <td style="padding:12px 16px;background:#f0f9ff;border-left:3px solid #0ea5e9;border-radius:6px;margin-bottom:8px;">
            <strong style="color:#0f172a;">1. Ajoutez vos services</strong><br>
            <span style="color:#64748b;font-size:13px;">Définissez vos prestations, tarifs et délais pour que l'IA puisse répondre aux questions de prix.</span>
        </td>
    </tr>
</table>
<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:16px;">
    <tr>
        <td style="padding:12px 16px;background:#f0f9ff;border-left:3px solid #0ea5e9;border-radius:6px;margin-bottom:8px;">
            <strong style="color:#0f172a;">2. Personnalisez l'IA</strong><br>
            <span style="color:#64748b;font-size:13px;">Écrivez les instructions pour l'assistant IA qui répondra à vos clients.</span>
        </td>
    </tr>
</table>
<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:16px;">
    <tr>
        <td style="padding:12px 16px;background:#f0f9ff;border-left:3px solid #0ea5e9;border-radius:6px;margin-bottom:8px;">
            <strong style="color:#0f172a;">3. Testez l'IA</strong><br>
            <span style="color:#64748b;font-size:13px;">Utilisez le chat de simulation pour voir comment l'IA répond avant de connecter WhatsApp.</span>
        </td>
    </tr>
</table>

<table width="100%" cellpadding="0" cellspacing="0" style="margin:24px 0;">
    <tr>
        <td align="center">
            <a href="{{ $dashboardUrl }}" style="display:inline-block;padding:14px 28px;background:#0ea5e9;color:#ffffff;border-radius:8px;font-weight:700;font-size:15px;text-decoration:none;">
                Accéder au tableau de bord →
            </a>
        </td>
    </tr>
</table>

<p style="margin:0;font-size:13px;color:#94a3b8;text-align:center;">
    Besoin d'aide ? Répondez à cet email ou contactez-nous via le formulaire de contact.
</p>
@endslot

@endcomponent
