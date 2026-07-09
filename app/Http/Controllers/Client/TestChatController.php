<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\SandboxMessage;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $business = $user->business;
        $messages = SandboxMessage::where('business_id', $user->business_id)
            ->where('trigger', 'test_chat')
            ->orderBy('created_at')
            ->get();

        return view('client.test-chat.index', compact('user', 'business', 'messages'));
    }

    public function send(Request $request)
    {
        $user = Auth::user();
        $business = $user->business;

        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $userMessage = trim($request->input('message'));

        // Save user message
        SandboxMessage::create([
            'business_id'  => $user->business_id,
            'to'           => 'test_chat',
            'contact_name' => $user->name,
            'type'         => 'text',
            'content'      => $userMessage,
            'trigger'      => 'test_chat',
        ]);

        // Get AI reply
        $gemini = new GeminiService();
        $aiReply = $gemini->chat($business, $userMessage);

        if ($aiReply) {
            SandboxMessage::create([
                'business_id'  => $user->business_id,
                'to'           => 'test_chat',
                'contact_name' => 'AI',
                'type'         => 'text',
                'content'      => $aiReply,
                'trigger'      => 'test_chat_reply',
            ]);
        } else {
            $aiReply = __('app.client.test_chat.no_reply');
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'reply'   => $aiReply,
            ]);
        }

        return redirect()->route('c.test-chat');
    }

    public function clear()
    {
        $user = Auth::user();
        SandboxMessage::where('business_id', $user->business_id)
            ->where('trigger', 'test_chat')
            ->delete();
        SandboxMessage::where('business_id', $user->business_id)
            ->where('trigger', 'test_chat_reply')
            ->delete();

        return redirect()->route('c.test-chat')->with('success', __('app.client.test_chat.cleared'));
    }
}
