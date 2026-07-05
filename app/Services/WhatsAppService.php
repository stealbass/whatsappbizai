<?php

namespace App\Services;

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
     * Envoie un message texte simple
     */
    public function sendText(string $to, string $message, string $phoneNumberId, string $token): bool
    {
        return $this->send($phoneNumberId, $token, [
            'messaging_product' => 'whatsapp',
            'recipient_type'    => 'individual',
            'to'                => $to,
            'type'              => 'text',
            'text'              => ['body' => $message, 'preview_url' => false],
        ]);
    }

    /**
     * Envoie un document PDF (devis ou facture)
     */
    public function sendDocument(
        string $to,
        string $mediaUrl,
        string $filename,
        string $caption,
        string $phoneNumberId,
        string $token
    ): bool {
        return $this->send($phoneNumberId, $token, [
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
     * Marque un message comme "lu"
     */
    public function markAsRead(string $messageId, string $phoneNumberId, string $token): bool
    {
        return $this->send($phoneNumberId, $token, [
            'messaging_product' => 'whatsapp',
            'status'            => 'read',
            'message_id'        => $messageId,
        ]);
    }

    /**
     * Envoie un message avec boutons de réponse rapide
     */
    public function sendButtons(
        string $to,
        string $bodyText,
        array  $buttons, // [['id' => 'btn_1', 'title' => 'Oui'], ...]
        string $phoneNumberId,
        string $token
    ): bool {
        $buttonList = array_map(fn($b) => [
            'type'  => 'reply',
            'reply' => ['id' => $b['id'], 'title' => $b['title']],
        ], $buttons);

        return $this->send($phoneNumberId, $token, [
            'messaging_product' => 'whatsapp',
            'recipient_type'    => 'individual',
            'to'                => $to,
            'type'              => 'interactive',
            'interactive'       => [
                'type' => 'button',
                'body' => ['text' => $bodyText],
                'action' => ['buttons' => $buttonList],
            ],
        ]);
    }

    /**
     * Parse le payload webhook Meta pour extraire le message entrant
     */
    public function parseInboundMessage(array $payload): ?array
    {
        try {
            $entry   = $payload['entry'][0] ?? null;
            $changes = $entry['changes'][0] ?? null;
            $value   = $changes['value'] ?? null;

            if (!$value || !isset($value['messages'])) {
                return null;
            }

            $message  = $value['messages'][0];
            $contact  = $value['contacts'][0] ?? null;
            $metadata = $value['metadata'] ?? null;

            $type    = $message['type'] ?? 'text';
            $content = match ($type) {
                'text'              => $message['text']['body'] ?? '',
                'button'            => $message['button']['text'] ?? '',
                'interactive'       => $message['interactive']['button_reply']['title']
                                    ?? $message['interactive']['list_reply']['title'] ?? '',
                'document'          => $message['document']['caption'] ?? '[document reçu]',
                'image'             => $message['image']['caption'] ?? '[image reçue]',
                'audio'             => '[message vocal reçu]',
                default             => '[media reçu]',
            };

            return [
                'message_id'       => $message['id'],
                'from'             => $message['from'],
                'contact_name'     => $contact['profile']['name'] ?? null,
                'type'             => $type,
                'content'          => $content,
                'phone_number_id'  => $metadata['phone_number_id'] ?? null,
                'timestamp'        => $message['timestamp'],
            ];

        } catch (\Throwable $e) {
            Log::error('WhatsApp parse error', ['error' => $e->getMessage(), 'payload' => $payload]);
            return null;
        }
    }

    /**
     * Appel HTTP générique vers l'API Graph
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
