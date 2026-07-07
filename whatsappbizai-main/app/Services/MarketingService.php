<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Contact;
use Illuminate\Support\Facades\Log;

class MarketingService
{
    public function __construct(
        private readonly WhatsAppService $whatsapp
    ) {}

    /**
     * Envoie un message broadcast à une liste de contacts
     *
     * @param  Business  $business
     * @param  \Illuminate\Support\Collection|Contact[]  $contacts
     * @param  string  $message  Message texte à envoyer
     * @return array  ['sent' => int, 'failed' => int]
     */
    public function sendBroadcast(Business $business, $contacts, string $message): array
    {
        if (!$business->whatsapp_phone_number_id || !$business->whatsapp_access_token) {
            Log::warning('MarketingService: business sans config WhatsApp', ['id' => $business->id]);
            return ['sent' => 0, 'failed' => 0];
        }

        $sent   = 0;
        $failed = 0;

        foreach ($contacts as $contact) {
            if (!$contact->whatsapp_number) {
                $failed++;
                continue;
            }

            // Personnalise le message avec le prénom du contact
            $personalised = $this->personalise($message, $contact);

            $ok = $this->whatsapp->sendText(
                $contact->whatsapp_number,
                $personalised,
                $business->whatsapp_phone_number_id,
                $business->whatsapp_access_token
            );

            $ok ? $sent++ : $failed++;

            // Pause 200ms entre chaque envoi pour respecter les rate limits Meta
            usleep(200_000);
        }

        Log::info('Broadcast WhatsApp terminé', [
            'business' => $business->name,
            'sent'     => $sent,
            'failed'   => $failed,
        ]);

        return compact('sent', 'failed');
    }

    /**
     * Envoie un PDF (devis ou facture) via WhatsApp à un contact
     */
    public function sendPdfToContact(
        Business $business,
        Contact  $contact,
        string   $pdfUrl,
        string   $filename,
        string   $caption
    ): bool {
        return $this->whatsapp->sendDocument(
            $contact->whatsapp_number,
            $pdfUrl,
            $filename,
            $caption,
            $business->whatsapp_phone_number_id,
            $business->whatsapp_access_token
        );
    }

    /**
     * Remplace les variables dans le message
     * Supporte : {{nom}}, {{prenom}}, {{entreprise}}
     */
    private function personalise(string $message, Contact $contact): string
    {
        $name      = $contact->name     ?? 'cher client';
        $firstname = explode(' ', $name)[0];
        $company   = $contact->company  ?? '';

        return str_replace(
            ['{{nom}}', '{{prenom}}', '{{entreprise}}', '{{name}}'],
            [$name,     $firstname,   $company,         $name],
            $message
        );
    }
}
