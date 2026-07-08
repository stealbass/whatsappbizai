<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Bienvenue sur " . ($this->user->business?->name ?? 'WhatsAppBizAI') . " !",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.welcome',
            with: [
                'userName'  => $this->user->name,
                'businessName' => $this->user->business?->name ?? 'votre entreprise',
                'loginUrl'  => url('login'),
                'dashboardUrl' => url('dashboard'),
                'settingsUrl' => url('client/settings/whatsapp'),
                'servicesUrl' => url('client/services'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
