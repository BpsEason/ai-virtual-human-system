<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageSent;
use App\Http\Responses\ApiResponse;
use App\Models\Conversation;
use App\Services\ChatService;
use Illuminate\Http\Request;

class ChatMessageController extends Controller
{
    protected $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function index(Conversation $conversation)
    {
        $this->authorize('view', $conversation);
        $messages = $conversation->messages()->orderBy('created_at', 'asc')->get();
        return ApiResponse::success($messages);
    }

    public function store(Request $request, Conversation $conversation)
    {
        $this->authorize('update', $conversation);
        $request->validate([
            'message' => 'required|string',
        ]);

        $userMessage = $conversation->messages()->create([
            'sender' => 'user',
            'content' => $request->message,
        ]);

        // 觸發事件
        event(new ChatMessageSent($userMessage));

        // 模擬 AI 回覆
        $aiResponse = $this->chatService->generateResponse($conversation, $request->message);

        $aiMessage = $conversation->messages()->create([
            'sender' => 'ai',
            'content' => $aiResponse,
        ]);

        event(new ChatMessageSent($aiMessage));

        return ApiResponse::success($userMessage, 'Message sent successfully', 201);
    }
}
