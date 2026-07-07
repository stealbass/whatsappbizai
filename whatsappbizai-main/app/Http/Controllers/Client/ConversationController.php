<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $conversations = Conversation::where('business_id', $user->business_id)
            ->with('contact')
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('client.conversations.index', compact('user', 'conversations'));
    }

    public function show(Conversation $conversation)
    {
        $user = Auth::user();
        abort_unless($conversation->business_id === $user->business_id, 403);
        $conversation->load(['contact', 'messages' => function ($q) {
            $q->orderBy('created_at');
        }]);

        return view('client.conversations.show', compact('user', 'conversation'));
    }
}
