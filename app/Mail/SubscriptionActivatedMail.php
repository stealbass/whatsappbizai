<?php

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionActivatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Subscription $subscription,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🎉 Votre abonnement " . ucfirst($this->subscription->plan) . " est activé !",
        );
    }

    public function content(): Content
    {
        $business = $this->subscription->business;
        return new Content(
            markdown: 'emails.subscription_activated',
            with: [
                'userName'      => $business->owner_name ?? $business->name,
                'businessName'  => $business->name,
                'plan'          => ucfirst($this->subscription->plan),
                'amount'        => number_format($this->subscription->amount ?? 0, 0, ',', ' '),
                'currency'      => $this->subscription->currency ?? 'XAF',
                'cycle'         => $this->subscription->billing_cycle === 'yearly' ? 'Annuel' : 'Mensuel',
                'endsAt'        => $this->subscription->ends_at?->format('d/m/Y') ?? 'Illimité',
                'dashboardUrl'  => url('dashboard'),
                'billingUrl'    => url('client/settings/billing'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
