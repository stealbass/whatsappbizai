<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $senderName,
        public string $senderEmail,
        public string $subject,
        public string $message,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "📩 Nouveau message de contact : {$this->subject}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact_form',
            with: [
                'senderName'  => $this->senderName,
                'senderEmail' => $this->senderEmail,
                'subject'     => $this->subject,
                'message'     => $this->message,
                'adminUrl'    => url('/admin'),
                'replyTo'     => $this->senderEmail,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
