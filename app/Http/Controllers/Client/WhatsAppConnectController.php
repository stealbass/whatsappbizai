<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Handles Meta Embedded Signup OAuth flow.
 *
 * Flow:
 *  1. Client clicks "Connecter mon WhatsApp"
 *  2. Meta popup opens (JS SDK), user logs in and picks their WABA
 *  3. Meta returns a short-lived `code`
 *  4. This controller exchanges it for a long-lived system user token
 *     and fetches the Phone Number ID automatically
 *  5. Credentials saved to Business → sandbox_mode disabled
 */
class WhatsAppConnectController extends Controller
{
    public function connect(Request $request): JsonResponse
    {
        $request->validate(['code' => 'required|string']);

        $business = Auth::user()?->business;
        if (!$business) {
            return response()->json(['success' => false, 'error' => 'No business found'], 422);
        }

        $appId     = config('whatsapp.meta_app_id');
        $appSecret = config('whatsapp.meta_app_secret');
        $configId  = config('whatsapp.meta_config_id');
        $apiVersion = config('whatsapp.api_version', 'v20.0');

        if (!$appId || !$appSecret) {
            return response()->json(['success' => false, 'error' => 'Meta App not configured'], 422);
        }

        try {
            // Step 1: Exchange code for a short-lived token
            $tokenResp = Http::get("https://graph.facebook.com/{$apiVersion}/oauth/access_token", [
                'client_id'     => $appId,
                'client_secret' => $appSecret,
                'code'          => $request->input('code'),
            ]);

            if ($tokenResp->failed()) {
                Log::error('WhatsApp Embedded Signup: token exchange failed', [
                    'status' => $tokenResp->status(),
                    'body'   => $tokenResp->body(),
                ]);
                return response()->json(['success' => false, 'error' => 'Token exchange failed: '.$tokenResp->body()], 422);
            }

            $accessToken = $tokenResp->json('access_token');

            // Step 2: Get WABA info — list phone numbers linked to this token
            $phoneResp = Http::withToken($accessToken)
                ->get("https://graph.facebook.com/{$apiVersion}/me/businesses");

            if ($phoneResp->failed()) {
                Log::error('WhatsApp Embedded Signup: businesses fetch failed', [
                    'body' => $phoneResp->body(),
                ]);
                return response()->json(['success' => false, 'error' => 'Could not fetch business data'], 422);
            }

            // Step 3: Fetch WABA and phone number ID via the debug_token endpoint
            $debugResp = Http::get("https://graph.facebook.com/debug_token", [
                'input_token'  => $accessToken,
                'access_token' => "{$appId}|{$appSecret}",
            ]);

            $wabaId = null;
            $phoneNumberId = null;

            if ($debugResp->ok()) {
                $granularScopes = $debugResp->json('data.granular_scopes') ?? [];
                foreach ($granularScopes as $scope) {
                    if ($scope['scope'] === 'whatsapp_business_management') {
                        $wabaId = $scope['target_ids'][0] ?? null;
                    }
                    if ($scope['scope'] === 'whatsapp_business_messaging') {
                        $phoneNumberId = $scope['target_ids'][0] ?? null;
                    }
                }
            }

            // Step 4: Save to business
            $updateData = [
                'whatsapp_access_token'         => $accessToken,
                'sandbox_mode'                  => false,
            ];

            if ($wabaId) {
                $updateData['whatsapp_business_account_id'] = $wabaId;
            }
            if ($phoneNumberId) {
                $updateData['whatsapp_phone_number_id'] = $phoneNumberId;
            }

            $business->update($updateData);

            Log::info('WhatsApp Embedded Signup: success', [
                'business'       => $business->name,
                'phone_id'       => $phoneNumberId,
                'waba_id'        => $wabaId,
            ]);

            return response()->json([
                'success'        => true,
                'phone_id'       => $phoneNumberId,
                'waba_id'        => $wabaId,
            ]);

        } catch (\Throwable $e) {
            Log::error('WhatsApp Embedded Signup: exception', ['message' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
