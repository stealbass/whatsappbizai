<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessWhatsAppMessage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * GET /api/webhook/whatsapp
     * Vérification du webhook par Meta lors de la configuration
     */
    public function verify(Request $request): Response
    {
        $mode      = $request->query('hub_mode');
        $token     = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        $expectedToken = config('whatsapp.verify_token');

        if ($mode === 'subscribe' && $token === $expectedToken) {
            Log::info('WhatsApp webhook vérifié avec succès');
            return response($challenge, 200);
        }

        Log::warning('WhatsApp webhook: token invalide', ['token' => $token]);
        return response('Forbidden', 403);
    }

    /**
     * POST /api/webhook/whatsapp
     * Réception des messages entrants depuis Meta
     */
    public function handle(Request $request): Response
    {
        $payload = $request->all();

        Log::debug('WhatsApp webhook reçu', ['payload' => $payload]);

        // Meta exige une réponse 200 immédiate, on dispatch en queue
        $object = $payload['object'] ?? null;

        if ($object === 'whatsapp_business_account') {
            ProcessWhatsAppMessage::dispatch($payload);
        }

        return response('OK', 200);
    }
}
