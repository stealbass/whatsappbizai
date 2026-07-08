@component('email.layouts.email')

@slot('content')
<h2 style="margin:0 0 16px;font-size:20px;font-weight:700;color:#0f172a;">
    💰 Nouvelle preuve de paiement
</h2>

<p style="margin:0 0 16px;font-size:15px;color:#475569;line-height:1.7;">
    Un client a soumis une preuve de paiement manuel (MoMo/Orange/Wave). Veuillez vérifier et activer l'abonnement.
</p>

<table width="100%" cellpadding="0" cellspacing="0" style="background:#fffbeb;border-radius:8px;margin:20px 0;border:1px solid #fde68a;">
    <tr>
        <td style="padding:20px;">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="padding:6px 0;font-size:14px;color:#64748b;">Entreprise</td>
                    <td style="padding:6px 0;font-size:14px;font-weight:700;color:#0f172a;text-align:right;">{{ $businessName }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0;font-size:14px;color:#64748b;">Plan</td>
                    <td style="padding:6px 0;font-size:14px;font-weight:700;color:#0f172a;text-align:right;">{{ $plan }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0;font-size:14px;color:#64748b;">Montant</td>
                    <td style="padding:6px 0;font-size:14px;font-weight:700;color:#0f172a;text-align:right;">{{ $amount }} {{ $currency }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0;font-size:14px;color:#64748b;">Méthode</td>
                    <td style="padding:6px 0;font-size:14px;font-weight:700;color:#0f172a;text-align:right;">{{ $method }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0;font-size:14px;color:#64748b;">Référence</td>
                    <td style="padding:6px 0;font-size:14px;font-weight:700;color:#0f172a;text-align:right;">{{ $reference }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0;font-size:14px;color:#64748b;">Téléphone</td>
                    <td style="padding:6px 0;font-size:14px;font-weight:700;color:#0f172a;text-align:right;">{{ $phone }}</td>
                </tr>
                @if($notes)
                <tr>
                    <td style="padding:6px 0;font-size:14px;color:#64748b;">Notes</td>
                    <td style="padding:6px 0;font-size:14px;color:#0f172a;text-align:right;">{{ $notes }}</td>
                </tr>
                @endif
                <tr>
                    <td style="padding:6px 0;font-size:14px;color:#64748b;">Date</td>
                    <td style="padding:6px 0;font-size:14px;color:#0f172a;text-align:right;">{{ $createdAt }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

@if($screenshot)
<p style="margin:0 0 8px;font-size:14px;color:#475569;">📸 Capture d'écran :</p>
<p style="margin:0 0 20px;">
    <a href="{{ $screenshot }}" style="color:#0ea5e9;font-size:13px;">Voir la capture d'écran</a>
</p>
@endif

<table width="100%" cellpadding="0" cellspacing="0" style="margin:24px 0;">
    <tr>
        <td align="center">
            <a href="{{ $adminUrl }}" style="display:inline-block;padding:14px 28px;background:#f59e0b;color:#ffffff;border-radius:8px;font-weight:700;font-size:15px;text-decoration:none;">
                Vérifier le paiement →
            </a>
        </td>
    </tr>
</table>
@endslot

@endcomponent
