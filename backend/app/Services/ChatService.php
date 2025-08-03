<?php

namespace App\Services;

use App\Models\Conversation;
use Illuminate\Support\Facades\Log;

class ChatService
{
    /**
     * 模擬 RAG Pipeline 流程，生成 AI 回覆。
     */
    public function generateResponse(Conversation $conversation, string $userMessage): string
    {
        // 1. 檢索相關知識
        // 這部分需要向量資料庫 (e.g., ChromaDB, Pinecone) 的實作
        // $relevantChunks = $this->vectorStoreService->retrieve($userMessage);

        // 2. 獲取角色 Persona
        $characterPersona = $conversation->character->persona;

        // 3. 獲取歷史對話
        $historyMessages = $conversation->messages()->orderBy('created_at')->get();
        $history = $historyMessages->map(fn($msg) => ['role' => $msg->sender, 'content' => $msg->content])->toArray();

        // 4. 拼接上下文，傳給 LLM
        $prompt = $this->buildPrompt($characterPersona, $history, $userMessage);

        // 5. 呼叫 LLM 服務 (e.g., OpenAI, Azure)
        // 這部分需要實際的 API 呼叫，這裡僅為模擬
        Log::info('模擬 LLM 呼叫', ['prompt' => $prompt]);

        // 模擬一個基於 Persona 的簡單回覆
        return "好的，我知道了。你剛才說：'{$userMessage}'。作為 {$characterPersona['name']}，我會這樣回覆你。";
    }

    protected function buildPrompt(array $persona, array $history, string $userMessage): string
    {
        $prompt = "你現在扮演一個名為 {$persona['name']} 的角色。{$persona['description']}。請根據以下對話歷史和你的 Persona 來回覆。\n\n";

        foreach ($history as $msg) {
            $prompt .= ucfirst($msg['role']) . ": " . $msg['content'] . "\n";
        }

        $prompt .= "User: " . $userMessage . "\n";
        $prompt .= "AI: ";

        return $prompt;
    }
}
