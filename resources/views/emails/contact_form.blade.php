@component('email.layouts.email')

@slot('content')
<h2 style="margin:0 0 16px;font-size:20px;font-weight:700;color:#0f172a;">
    📩 Nouveau message de contact
</h2>

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border-radius:8px;margin:20px 0;border:1px solid #e2e8f0;">
    <tr>
        <td style="padding:20px;">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="padding:6px 0;font-size:14px;color:#64748b;">De</td>
                    <td style="padding:6px 0;font-size:14px;font-weight:700;color:#0f172a;text-align:right;">{{ $senderName }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0;font-size:14px;color:#64748b;">Email</td>
                    <td style="padding:6px 0;font-size:14px;color:#0f172a;text-align:right;">
                        <a href="mailto:{{ $senderEmail }}" style="color:#0ea5e9;">{{ $senderEmail }}</a>
                    </td>
                </tr>
                <tr>
                    <td style="padding:6px 0;font-size:14px;color:#64748b;">Sujet</td>
                    <td style="padding:6px 0;font-size:14px;font-weight:700;color:#0f172a;text-align:right;">{{ $subject }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:20px;margin:20px 0;">
    <p style="margin:0;font-size:15px;color:#334155;line-height:1.7;white-space:pre-wrap;">{{ $message }}</p>
</div>

<table width="100%" cellpadding="0" cellspacing="0" style="margin:24px 0;">
    <tr>
        <td align="center">
            <a href="mailto:{{ $senderEmail }}?subject=Re: {{ $subject }}" style="display:inline-block;padding:14px 28px;background:#0ea5e9;color:#ffffff;border-radius:8px;font-weight:700;font-size:15px;text-decoration:none;">
                Répondre →
            </a>
        </td>
    </tr>
</table>
@endslot

@endcomponent
