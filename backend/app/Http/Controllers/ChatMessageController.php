<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageSent;
use App\Http\Responses\ApiResponse;
use App\Models\ChatMessage;
use Illuminate\Http\Request;

class ChatMessageController extends Controller
{
    public function index(Request $request)
    {
        $messages = ChatMessage::where('character_id', $request->character_id)
            ->orderBy('created_at', 'asc')
            ->get();
        return ApiResponse::success($messages);
    }

    public function store(Request $request)
    {
        $request->validate([
            'character_id' => 'required|exists:characters,id',
            'sender' => 'required|in:user,ai',
            'message' => 'required|string',
        ]);

        $message = ChatMessage::create($request->all());

        // 觸發事件
        event(new ChatMessageSent($message));

        return ApiResponse::success($message, 'Message sent successfully', 201);
    }
}
