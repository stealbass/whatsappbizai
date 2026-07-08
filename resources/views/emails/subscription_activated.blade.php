@component('email.layouts.email')

@slot('content')
<h2 style="margin:0 0 16px;font-size:20px;font-weight:700;color:#0f172a;">
    🎉 Abonnement activé !
</h2>

<p style="margin:0 0 16px;font-size:15px;color:#475569;line-height:1.7;">
    Bonjour <strong>{{ $userName }}</strong>,
</p>

<p style="margin:0 0 16px;font-size:15px;color:#475569;line-height:1.7;">
    Votre abonnement <strong>{{ $plan }}</strong> pour <strong>{{ $businessName }}</strong> est maintenant actif. Merci pour votre confiance !
</p>

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0fdf4;border-radius:8px;margin:20px 0;">
    <tr>
        <td style="padding:20px;">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="padding:4px 0;font-size:14px;color:#64748b;">Plan</td>
                    <td style="padding:4px 0;font-size:14px;font-weight:700;color:#0f172a;text-align:right;">{{ $plan }}</td>
                </tr>
                <tr>
                    <td style="padding:4px 0;font-size:14px;color:#64748b;">Montant</td>
                    <td style="padding:4px 0;font-size:14px;font-weight:700;color:#0f172a;text-align:right;">{{ $amount }} {{ $currency }}</td>
                </tr>
                <tr>
                    <td style="padding:4px 0;font-size:14px;color:#64748b;">Facturation</td>
                    <td style="padding:4px 0;font-size:14px;font-weight:700;color:#0f172a;text-align:right;">{{ $cycle }}</td>
                </tr>
                <tr>
                    <td style="padding:4px 0;font-size:14px;color:#64748b;">Valide jusqu'au</td>
                    <td style="padding:4px 0;font-size:14px;font-weight:700;color:#0f172a;text-align:right;">{{ $endsAt }}</td>
                </tr>
            </table>
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
    Vous pouvez gérer votre abonnement depuis <a href="{{ $billingUrl }}" style="color:#0ea5e9;">vos paramètres de facturation</a>.
</p>
@endslot

@endcomponent
