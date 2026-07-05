<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Services\GeminiService;
use App\Services\MarketingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RetentionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $business = $user->business;

        $stats = [
            'inactive'   => Contact::where('business_id', $business->id)->where('status', 'client')->where('last_seen_at', '<', now()->subDays(30))->count(),
            'clients'    => Contact::where('business_id', $business->id)->where('status', 'client')->count(),
            'prospects'  => Contact::where('business_id', $business->id)->where('status', 'prospect')->count(),
            'high_value' => Contact::where('business_id', $business->id)->where('status', 'client')->where('total_invoiced', '>', 100000)->count(),
        ];

        return view('client.retention.index', compact('user', 'business', 'stats'));
    }

    public function send(Request $request, MarketingService $marketing)
    {
        $user = Auth::user();
        $business = $user->business;

        $data = $request->validate([
            'message'   => 'required|string|max:1024',
            'target'    => 'required|in:inactive_clients,all_clients,prospects,high_value',
            'objective' => 'required|in:retention,upsell,winback,referral',
        ]);

        if (!$business->whatsapp_phone_number_id || !$business->whatsapp_access_token) {
            return back()->with('error', 'WhatsApp non configuré.');
        }

        $q = Contact::where('business_id', $business->id)->whereNotNull('whatsapp_number');

        match($data['target']) {
            'inactive_clients' => $q->where('status', 'client')->where('last_seen_at', '<', now()->subDays(30)),
            'prospects'        => $q->where('status', 'prospect'),
            'high_value'       => $q->where('status', 'client')->where('total_invoiced', '>', 100000),
            default            => $q->where('status', 'client'),
        };

        $contacts = $q->get();

        if ($contacts->isEmpty()) {
            return back()->with('error', 'Aucun contact trouvé pour cette sélection.');
        }

        $result = $marketing->sendBroadcast($business, $contacts, $data['message']);

        return back()->with('success', "Campagne terminée — {$result['sent']} envoi(s) réussi(s)" . ($result['failed'] > 0 ? ", {$result['failed']} échec(s)" : ''));
    }

    public function draftAI()
    {
        $user = Auth::user();
        $business = $user->business;

        $objective = request('objective', 'retention');
        $target = request('target', 'inactive_clients');

        $goal = match($objective) {
            'retention' => 'Write a retention message to re-engage inactive clients with a special offer',
            'upsell'    => 'Write an upsell message to offer premium services to existing clients',
            'winback'   => 'Write a win-back message for clients who haven\'t purchased in 30+ days',
            'referral'  => 'Write a referral invitation message offering rewards for bringing new clients',
            default     => 'Write a professional marketing message',
        };

        $audience = match($target) {
            'inactive_clients' => 'inactive clients for 30+ days',
            'all_clients'      => 'all active clients',
            'prospects'        => 'prospects who haven\'t purchased yet',
            'high_value'       => 'high-value clients (> 100,000 XAF)',
            default            => 'all contacts',
        };

        $gemini = app(GeminiService::class);
        $draft = $gemini->draftBroadcast($business, $goal, $audience);

        return response()->json(['message' => $draft]);
    }
}
