<?php

namespace App\Jobs;

use App\Models\Business;
use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\GeminiService;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessWhatsAppMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 60;

    public function __construct(private readonly array $payload) {}

    public function handle(WhatsAppService $whatsapp, GeminiService $gemini): void
    {
        // 1. Parse le message entrant
        $parsed = $whatsapp->parseInboundMessage($this->payload);

        if (!$parsed) {
            Log::debug('Webhook ignoré (pas un message)');
            return;
        }

        $phoneNumberId = $parsed['phone_number_id'];

        // 2. Trouve le business associé au numéro WhatsApp
        $business = Business::where('whatsapp_phone_number_id', $phoneNumberId)
            ->where('is_active', true)
            ->first();

        if (!$business) {
            Log::warning('Aucun business trouvé pour phone_number_id', ['id' => $phoneNumberId]);
            return;
        }

        // 3. Trouve ou crée le contact
        $contact = Contact::firstOrCreate(
            ['business_id' => $business->id, 'whatsapp_number' => $parsed['from']],
            ['name' => $parsed['contact_name'] ?? $parsed['from'], 'status' => 'prospect']
        );

        $contact->update(['last_seen_at' => now()]);

        // 4. Trouve ou crée une conversation ouverte
        $conversation = Conversation::firstOrCreate(
            ['contact_id' => $contact->id, 'status' => 'open'],
            ['business_id' => $business->id, 'ai_enabled' => true, 'channel' => 'whatsapp']
        );

        $conversation->update(['last_message_at' => now()]);

        // 5. Enregistre le message entrant
        Message::create([
            'conversation_id'    => $conversation->id,
            'whatsapp_message_id'=> $parsed['message_id'],
            'direction'          => 'inbound',
            'type'               => $parsed['type'],
            'content'            => $parsed['content'],
            'status'             => 'delivered',
            'sent_at'            => now(),
        ]);

        // Marque comme lu
        $whatsapp->markAsRead($parsed['message_id'], $phoneNumberId, $business->whatsapp_access_token);

        // 6. Si l'IA est désactivée pour cette conversation, on s'arrête
        if (!$conversation->ai_enabled) {
            Log::info('IA désactivée pour cette conversation', ['conversation_id' => $conversation->id]);
            return;
        }

        // 7. Génère la réponse Gemini
        $reply = $gemini->generateReply($business, $conversation, $parsed['content']);

        if (!$reply) {
            Log::error('Gemini n\'a pas retourné de réponse', ['conversation_id' => $conversation->id]);
            return;
        }

        // 8. Envoie la réponse via WhatsApp
        $sent = $whatsapp->sendText(
            $parsed['from'],
            $reply,
            $phoneNumberId,
            $business->whatsapp_access_token
        );

        // 9. Enregistre la réponse envoyée
        Message::create([
            'conversation_id' => $conversation->id,
            'direction'       => 'outbound',
            'type'            => 'text',
            'content'         => $reply,
            'is_ai'           => true,
            'status'          => $sent ? 'sent' : 'failed',
            'sent_at'         => now(),
        ]);

        Log::info('Message traité et réponse envoyée', [
            'contact'  => $contact->name,
            'business' => $business->name,
            'sent'     => $sent,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('ProcessWhatsAppMessage échoué', [
            'error'   => $exception->getMessage(),
            'payload' => $this->payload,
        ]);
    }
}
