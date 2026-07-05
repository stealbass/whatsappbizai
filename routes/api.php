<?php

use App\Http\Controllers\Api\WhatsAppWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — WhatsAppBizAI
|--------------------------------------------------------------------------
*/

// WhatsApp Cloud API webhook (GET = vérification Meta, POST = messages entrants)
Route::get('/webhook/whatsapp', [WhatsAppWebhookController::class, 'verify'])
    ->name('webhook.whatsapp.verify');

Route::post('/webhook/whatsapp', [WhatsAppWebhookController::class, 'handle'])
    ->name('webhook.whatsapp.handle');
