<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Conversation;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function index(Request $request)
    {
        $conversations = Conversation::where('user_id', $request->user()->id)
            ->with('character')
            ->orderBy('updated_at', 'desc')
            ->get();
        return ApiResponse::success($conversations);
    }

    public function start(Request $request)
    {
        $request->validate([
            'character_id' => 'required|exists:characters,id',
        ]);

        $conversation = Conversation::create([
            'user_id' => $request->user()->id,
            'character_id' => $request->character_id,
        ]);

        return ApiResponse::success($conversation, 'Conversation started successfully', 201);
    }

    public function show(Conversation $conversation)
    {
        $this->authorize('view', $conversation);
        return ApiResponse::success($conversation->load('messages'));
    }
}
