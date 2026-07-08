<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Contact;
use App\Models\SandboxMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $apiVersion;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiVersion = config('whatsapp.api_version', 'v20.0');
        $this->baseUrl    = "https://graph.facebook.com/{$this->apiVersion}";
    }

    /**
     * Envoie un message texte — ou simule si sandbox_mode actif.
     */
    public function sendText(string $to, string $message, Business $business, string $trigger = 'manual'): bool
    {
        // Strip HTML tags + decode entities
        $plain = html_entity_decode(
            strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>', '</div>', '</li>'], "\n", $message)),
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );
        $plain = preg_replace("/\n{3,}/", "\n\n", trim($plain));

        if ($business->sandbox_mode) {
            return $this->simulateText($business, $to, $plain, $trigger);
        }

        return $this->send($business->whatsapp_phone_number_id, $business->whatsapp_access_token, [
            'messaging_product' => 'whatsapp',
            'recipient_type'    => 'individual',
            'to'                => $to,
            'type'              => 'text',
            'text'              => ['body' => $plain, 'preview_url' => false],
        ]);
    }

    /**
     * Envoie un document PDF — ou simule si sandbox_mode actif.
     */
    public function sendDocument(
        string $to,
        string $mediaUrl,
        string $filename,
        string $caption,
        Business $business,
        string $trigger = 'document'
    ): bool {
        if ($business->sandbox_mode) {
            return $this->simulateDocument($business, $to, $filename, $mediaUrl, $trigger);
        }

        return $this->send($business->whatsapp_phone_number_id, $business->whatsapp_access_token, [
            'messaging_product' => 'whatsapp',
            'recipient_type'    => 'individual',
            'to'                => $to,
            'type'              => 'document',
            'document'          => [
                'link'     => $mediaUrl,
                'caption'  => $caption,
                'filename' => $filename,
            ],
        ]);
    }

    /**
     * Marque un message comme lu (no-op en sandbox).
     */
    public function markAsRead(string $messageId, Business $business): bool
    {
        if ($business->sandbox_mode) return true;

        return $this->send($business->whatsapp_phone_number_id, $business->whatsapp_access_token, [
            'messaging_product' => 'whatsapp',
            'status'            => 'read',
            'message_id'        => $messageId,
        ]);
    }

    /**
     * Résout le nom du contact depuis son numéro.
     */
    private function resolveContactName(Business $business, string $to): string
    {
        $contact = Contact::where('business_id', $business->id)
            ->where('whatsapp_number', $to)
            ->first();

        return $contact?->name ?? $to;
    }

    /**
     * Enregistre un message texte simulé.
     */
    private function simulateText(Business $business, string $to, string $content, string $trigger): bool
    {
        SandboxMessage::create([
            'business_id'  => $business->id,
            'to'           => $to,
            'contact_name' => $this->resolveContactName($business, $to),
            'type'         => 'text',
            'content'      => $content,
            'trigger'      => $trigger,
        ]);
        Log::info('[SANDBOX] Message texte simulé', [
            'business' => $business->name,
            'to'       => $to,
            'trigger'  => $trigger,
        ]);
        return true;
    }

    /**
     * Enregistre un envoi de document simulé.
     */
    private function simulateDocument(Business $business, string $to, string $filename, string $mediaUrl, string $trigger): bool
    {
        SandboxMessage::create([
            'business_id'  => $business->id,
            'to'           => $to,
            'contact_name' => $this->resolveContactName($business, $to),
            'type'         => 'document',
            'content'      => $filename,
            'media_url'    => $mediaUrl,
            'trigger'      => $trigger,
        ]);
        Log::info('[SANDBOX] Document simulé', [
            'business' => $business->name,
            'to'       => $to,
            'file'     => $filename,
        ]);
        return true;
    }

    /**
     * Appel HTTP générique vers l'API Graph.
     */
    private function send(string $phoneNumberId, string $token, array $payload): bool
    {
        try {
            $response = Http::withToken($token)
                ->timeout(15)
                ->post("{$this->baseUrl}/{$phoneNumberId}/messages", $payload);

            if ($response->failed()) {
                Log::error('WhatsApp send failed', [
                    'status'  => $response->status(),
                    'body'    => $response->body(),
                    'payload' => $payload,
                ]);
                return false;
            }

            return true;

        } catch (\Throwable $e) {
            Log::error('WhatsApp exception', ['message' => $e->getMessage()]);
            return false;
        }
    }
}
