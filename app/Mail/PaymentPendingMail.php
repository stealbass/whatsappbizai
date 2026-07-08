<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentPendingMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Payment $payment,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "💰 Nouvelle preuve de paiement à vérifier",
        );
    }

    public function content(): Content
    {
        $business = $this->payment->business;
        return new Content(
            markdown: 'emails.payment_pending',
            with: [
                'businessName' => $business->name ?? 'N/A',
                'plan'         => ucfirst($this->payment->plan ?? 'N/A'),
                'amount'       => number_format($this->payment->amount ?? 0, 0, ',', ' '),
                'currency'     => $this->payment->currency ?? 'XAF',
                'method'       => $this->payment->method ?? 'N/A',
                'reference'    => $this->payment->reference ?? 'N/A',
                'phone'        => $this->payment->phone_number ?? 'N/A',
                'notes'        => $this->payment->notes ?? '',
                'screenshot'   => $this->payment->screenshot_path ? asset('storage/' . $this->payment->screenshot_path) : null,
                'adminUrl'     => url('/admin/payments'),
                'createdAt'    => $this->payment->created_at?->format('d/m/Y à H:i') ?? '',
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
