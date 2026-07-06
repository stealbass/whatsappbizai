<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Services\GeminiService;
use App\Services\MarketingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BroadcastController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $business = $user->business;

        $stats = [
            'total'    => Contact::where('business_id', $business->id)->whereNotNull('whatsapp_number')->count(),
            'clients'  => Contact::where('business_id', $business->id)->where('status', 'client')->whereNotNull('whatsapp_number')->count(),
            'prospects'=> Contact::where('business_id', $business->id)->where('status', 'prospect')->whereNotNull('whatsapp_number')->count(),
        ];

        return view('client.broadcast.index', compact('user', 'business', 'stats'));
    }

    public function send(Request $request, MarketingService $marketing)
    {
        $user = Auth::user();
        $business = $user->business;

        $data = $request->validate([
            'message' => 'required|string|max:100000',
            'target'  => 'required|in:all,clients,prospects',
        ]);

        if (!$business->whatsapp_phone_number_id || !$business->whatsapp_access_token) {
            return back()->with('error', __('app.client.flash.whatsapp_config_hint'));
        }

        $q = Contact::where('business_id', $business->id)->whereNotNull('whatsapp_number');
        if ($data['target'] === 'clients') $q->where('status', 'client');
        if ($data['target'] === 'prospects') $q->where('status', 'prospect');

        $contacts = $q->get();

        if ($contacts->isEmpty()) {
            return back()->with('error', __('app.client.flash.no_contacts_found'));
        }

        $result = $marketing->sendBroadcast($business, $contacts, $data['message']);

        return back()->with('success', "Broadcast terminé — {$result['sent']} envoi(s) réussi(s)" . ($result['failed'] > 0 ? ", {$result['failed']} échec(s)" : ''));
    }

    public function draftAI()
    {
        $user = Auth::user();
        $business = $user->business;

        $goal = request('goal', 'Promote our services and invite clients to contact us');
        $target = request('target', 'all');

        $audience = match($target) {
            'clients'   => 'existing clients',
            'prospects' => 'new prospects',
            default     => 'all contacts',
        };

        $gemini = app(GeminiService::class);
        $draft = $gemini->draftBroadcast($business, $goal, $audience);

        return response()->json(['message' => $draft]);
    }
}
