<?php

namespace App\Mail;

use App\Models\Business;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Business $business,
        public string $documentType,
        public string $documentNumber,
        public float $total,
        public string $currency,
        public string $pdfPath,
    ) {}

    public function envelope(): Envelope
    {
        $label = $this->documentType === 'invoice' ? 'Facture' : 'Devis';
        return new Envelope(
            subject: "{$label} {$this->documentNumber} — " . ($this->business->name ?? 'WhatsAppBizAI'),
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: $this->buildHtml(),
        );
    }

    private function buildHtml(): string
    {
        $sn = $this->business->name ?? 'WhatsAppBizAI';
        $type = $this->documentType === 'invoice' ? 'facture' : 'devis';
        $typeCapitalized = $this->documentType === 'invoice' ? 'Facture' : 'Devis';
        $totalFormatted = number_format($this->total, 0, ',', ' ');

        return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:32px 16px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08);">
                    <tr>
                        <td style="background:linear-gradient(135deg,#0ea5e9,#0284c7);padding:20px 32px;text-align:center;">
                            <h1 style="margin:0;color:#fff;font-size:18px;font-weight:800;">🟢 {$sn}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;">
                            <h2 style="margin:0 0 16px;color:#1e293b;font-size:20px;">Votre {$type} {$this->documentNumber}</h2>
                            <p style="margin:0 0 8px;color:#475569;font-size:14px;line-height:1.6;">
                                Veuillez trouver ci-joint votre {$type} <strong>{$this->documentNumber}</strong> d'un montant de
                                <strong>{$totalFormatted} {$this->currency}</strong>.
                            </p>
                            <p style="margin:0 0 24px;color:#475569;font-size:14px;line-height:1.6;">
                                Vous pouvez consulter le document en pièce jointe de cet email.
                            </p>
                            <table cellpadding="0" cellspacing="0" style="width:100%;background:#f0f9ff;border:1px solid #bae6fd;border-radius:8px;padding:16px;">
                                <tr>
                                    <td style="padding:8px 16px;">
                                        <p style="margin:0;font-size:13px;color:#0369a1;">
                                            📎 <strong>{$typeCapitalized} {$this->documentNumber}</strong> — {$totalFormatted} {$this->currency}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#f8fafc;padding:16px 32px;border-top:1px solid #e2e8f0;text-align:center;">
                            <p style="margin:0;font-size:11px;color:#94a3b8;">© {date('Y')} {$sn} · <a href="https://whatsappbizai.com" style="color:#0ea5e9;">whatsappbizai.com</a></p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }

    public function attachments(): array
    {
        return [
            Attachment::fromStorageDisk('public', $this->pdfPath)
                ->as("{$this->documentNumber}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
