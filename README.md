# AI 虛擬人互動管理系統 - 開發者指南

本專案是一個基於 **Laravel** 後端 API 和 **Vue3 + Vite** 前端的 AI 虛擬人互動管理系統，透過 **Docker Compose** 實現一鍵部署。本文件以問答形式介紹系統架構、核心功能、部署方式與 AI 模型串接方式，幫助開發者快速理解並整合 AI 功能（如 RAG 與 LLM）。

---

## 常見問題與解答

### 1. 這個系統的主要功能是什麼？

**答**:  
系統提供一個完整的 AI 虛擬人互動平台，包含以下核心功能：  
- **身份驗證**: 使用者註冊、登入、登出，支援 Admin/User 角色（基於 Laravel Sanctum）。  
- **AI 虛擬人管理**: 管理員可建立、編輯與刪除 AI 角色（含 JSON 格式的 Persona）。  
- **知識庫管理**: 支援上傳 txt、pdf、docx 文件作為 AI 知識來源，預留向量化接口。  
- **即時聊天**: 使用者與 AI 角色進行即時對話，支援歷史紀錄與 Redis 事件廣播。  
- **儀表板**: 管理員專用，展示用戶數、角色數與對話數等統計數據。

### 2. 系統的架構是什麼樣的？如何實現前後端分離？

**答**:  
系統採用前後端分離架構，透過 Docker 容器化確保環境一致性。以下是架構圖與說明：

```
+-------------------------+
|   Browser (Vue3 + Vite) |
|   - SPA Frontend        |
|   - API Requests (Axios)|
+-------------------------+
           ↑↓ HTTP/WS
+-------------------------+
|   Nginx (Reverse Proxy) |
|   - Routes to PHP-FPM   |
|   - Serves Static Files |
+-------------------------+
           ↑↓
+-------------------------+    +-------------------------+
|   PHP-FPM (Laravel API) |    |   Node.js (Vite Server) |
|   - RESTful Endpoints   |    |   - Hot Module Reload   |
|   - Sanctum Auth        |    |   - Frontend Dev Env    |
|   - Event Broadcasting  |    +-------------------------+
+-------------------------+
           ↑↓
+-------------------------+
|   MySQL (Data Storage)  |
|   - Users, Characters   |
|   - Documents, Messages |
+-------------------------+
           ↑↓
+-------------------------+
|   Redis (Cache & Events)|
|   - Session Management  |
|   - Real-time Broadcast |
+-------------------------+
```

- **前端層 (Vue3 + Vite)**: Vue3 實現單頁應用，Axios 發送 API 請求，Vite 提供熱模組重載。  
- **Web 伺服器層 (Nginx)**: 反向代理，路由 `/api` 至 PHP-FPM，處理靜態檔案。  
- **後端層 (Laravel API)**: 提供 RESTful API，Sanctum 權杖驗證，Redis 支援即時聊天。  
- **資料層**: MySQL 儲存結構化資料（users、characters 等），Redis 處理快取與事件。  
- **容器化層**: Docker Compose 協調 Nginx、PHP-FPM、MySQL、Redis、Node.js。

### 3. 如何設置與運行專案？

**答**:  
請按照以下步驟設置與運行專案：  
1. **環境準備**: 安裝 Docker Desktop 與 Git，確保端口 `80`、`3306`、`6379`、`5173` 未被佔用。  
2. **複製專案**:  
   ```bash
   git clone <repository-url>
   cd ai-virtual-human-system
   ```
3. **啟動容器**:  
   ```bash
   docker-compose up -d --build
   ```
4. **配置後端**:  
   ```bash
   docker-compose exec php bash
   composer create-project laravel/laravel .
   composer install
   cp .env.example .env
   php artisan key:generate
   php artisan migrate
   exit
   ```
5. **安裝前端依賴**:  
   ```bash
   docker-compose exec node bash
   npm install
   exit
   ```
6. **啟動前端伺服器**:  
   ```bash
   docker-compose exec node npm run dev
   ```
7. **訪問應用**: 前端 (`http://localhost:5173`), 後端 API (`http://localhost/api`)。

### 4. 如何在 Laravel 中串接 AI 模型（如 RAG 與 LLM）？

**答**:  
系統預留了 AI 模型串接接口，支援知識檢索（RAG）與對話生成（LLM），使用 Laravel 的 `Http` facade 或 SDK 與外部 AI 服務整合。以下是兩個核心串接點：

#### (1) 知識檢索（RAG）——KnowledgeBaseService
- **目的**: 將上傳的文件（txt、pdf、docx）轉為向量，儲存至向量資料庫（如 Pinecone、ChromaDB），供 RAG 檢索使用。  
- **串接方式**:  
  1. 使用 Apache Tika 或 pdf2text 解析文件為純文字。  
  2. 將文字分塊（chunks，例如每 500 字）。  
  3. 使用嵌入模型（HuggingFace、OpenAI）生成向量。  
  4. 透過向量資料庫的 PHP SDK 或 REST API（如 Pinecone 的 `upsert`）儲存向量。  
- **關鍵程式碼** (`KnowledgeBaseService.php`):

```php
<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class KnowledgeBaseService
{
    /**
     * 上傳文件並建立文件紀錄，預留向量化邏輯。
     * @param UploadedFile $file 上傳的文件
     * @param int $userId 上傳者的使用者 ID
     * @return Document 新建立的文件記錄
     */
    public function uploadDocument(UploadedFile $file, int $userId): Document
    {
        // 儲存文件到 public 磁碟
        $path = $file->store('knowledge_base', 'public');

        // 建立文件記錄
        $document = Document::create([
            'user_id' => $userId,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'status' => 'uploaded',
        ]);

        // 實作向量化邏輯
        // 1. 解析文件為純文字（示例：使用 Apache Tika）
        // $text = $this->parseDocumentToText($file);
        // 2. 分塊（每 500 字）
        // $chunks = $this->splitTextIntoChunks($text, 500);
        // 3. 生成向量（使用 OpenAI Embeddings API）
        // $embeddings = $this->generateEmbeddings($chunks);
        // 4. 儲存至 Pinecone
        // Http::withHeaders(['Api-Key' => env('PINECONE_API_KEY')])
        //     ->post('https://<index>.pinecone.io/vectors/upsert', [
        //         'vectors' => array_map(fn($chunk, $i) => [
        //             'id' => "doc_{$document->id}_chunk_{$i}",
        //             'values' => $embeddings[$i],
        //             'metadata' => ['document_id' => $document->id]
        //         ], $chunks, array_keys($chunks))
        //     ]);

        // 更新狀態
        $document->update(['status' => 'processed']);
        return $document;
    }
}
```

#### (2) 對話生成（LLM）——ChatService
- **目的**: 根據角色 Persona、對話歷史與使用者輸入，呼叫 LLM（如 OpenAI、Azure OpenAI）生成回覆。  
- **串接方式**:  
  1. 拼接 Prompt，包含 Persona、歷史訊息與使用者輸入。  
  2. 使用 Laravel 的 `Http` facade 或 Guzzle 呼叫 LLM API（如 OpenAI 的 `chat/completions`）。  
  3. 提取回覆內容（`choices[0].message.content`）並儲存。  
- **關鍵程式碼** (`ChatService.php`):

```php
<?php

namespace App\Services;

use App\Models\Conversation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatService
{
    /**
     * 生成 AI 回覆，支援 LLM 整合。
     * @param Conversation $conversation 當前對話
     * @param string $userMessage 使用者輸入的訊息
     * @return string AI 生成的回覆
     */
    public function generateResponse(Conversation $conversation, string $userMessage): string
    {
        // 1. 檢索相關知識（RAG，選用）
        // $chunks = $this->vectorStoreService->retrieve($userMessage);

        // 2. 獲取角色 Persona
        $characterPersona = $conversation->character->persona;

        // 3. 獲取對話歷史
        $historyMessages = $conversation->messages()->orderBy('created_at')->get();
        $history = $historyMessages->map(fn($msg) => ['role' => $msg->sender, 'content' => $msg->content])->toArray();

        // 4. 拼接 Prompt
        $prompt = $this->buildPrompt($characterPersona, $history, $userMessage);

        // 5. 呼叫 LLM（以 OpenAI 為例）
        // $response = Http::withHeaders([
        //     'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
        //     'Content-Type' => 'application/json',
        // ])->post('https://api.openai.com/v1/chat/completions', [
        //     'model' => 'gpt-4',
        //     'messages' => [
        //         ['role' => 'system', 'content' => "你是一個名為 {$characterPersona['name']} 的角色。{$characterPersona['description']}"],
        //         ...$history,
        //         ['role' => 'user', 'content' => $userMessage],
        //     ],
        //     'max_tokens' => 500,
        // ]);
        // $aiResponse = $response->json()['choices'][0]['message']['content'];

        // 目前模擬回覆
        Log::info('模擬 LLM 呼叫', ['prompt' => $prompt]);
        return "好的，我知道了。你剛說：'{$userMessage}'。作為 {$characterPersona['name']}，我會這樣回覆你。";
    }

    /**
     * 建立 LLM 的 Prompt。
     * @param array $persona 角色設定
     * @param array $history 對話歷史
     * @param string $userMessage 使用者輸入
     * @return string 拼接完成的 Prompt
     */
    protected function buildPrompt(array $persona, array $history, string $userMessage): string
    {
        $prompt = "你現在扮演一個名為 {$persona['name']} 的角色。{$persona['description']}。請根據以下對話歷史和你的 Persona 來回覆。\n\n";
        foreach ($history as $msg) {
            $prompt .= ucfirst($msg['role']) . ": " . $msg['content'] . "\n";
        }
        $prompt .= "User: " . $userMessage . "\nAI: ";
        return $prompt;
    }
}
```

- **整合建議**:  
  - **向量資料庫**: Pinecone、ChromaDB、Weaviate，透過 REST API 或 PHP SDK 實現 `upsert` 與 `retrieve`。  
  - **LLM 服務**: OpenAI、Azure OpenAI、Anthropic，優先使用 REST API 呼叫，確保 Laravel 相容性。  
  - **環境變數**: 在 `.env` 中配置 API Key（如 `OPENAI_API_KEY`、`PINECONE_API_KEY`）。  

### 5. 系統如何實現身份驗證與權限管理？

**答**:  
- **身份驗證**: 使用 Laravel Sanctum 提供權杖驗證，前端透過 Axios 攔截器附加 `Bearer` 權杖。  
- **權限管理**: 使用 Laravel Gate 與 Policy，管理員（`role=admin`）可管理 AI 角色，普通使用者僅操作自己的資料。  
- **程式碼示例** (`AuthController.php`):

```php
<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return ApiResponse::success(['user' => $user, 'token' => $token], 'User registered successfully', 201);
    }
}
```

### 6. 如何實現即時聊天功能？

**答**:  
- 使用 **Laravel Event Broadcaster** 與 **Redis** 實現訊息即時推播。  
- 使用者發送訊息後，觸發 `ChatMessageSent` 事件，前端訂閱 WebSocket 通道（`chat.{conversation_id}`）。  
- **程式碼示例** (`ChatMessageController.php`):

```php
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

    public function store(Request $request, Conversation $conversation)
    {
        $this->authorize('update', $conversation);
        $request->validate(['message' => 'required|string']);

        $userMessage = $conversation->messages()->create([
            'sender' => 'user',
            'content' => $request->message,
        ]);

        event(new ChatMessageSent($userMessage));

        $aiResponse = $this->chatService->generateResponse($conversation, $request->message);
        $aiMessage = $conversation->messages()->create([
            'sender' => 'ai',
            'content' => $aiResponse,
        ]);

        event(new ChatMessageSent($aiMessage));

        return ApiResponse::success($userMessage, 'Message sent successfully', 201);
    }
}
```

### 7. 前端如何與後端交互？

**答**:  
- 前端使用 Axios（`services/api.js`）發送 HTTP 請求至 `/api` 端點。  
- `useAuth.js` 管理使用者狀態，自動附加權杖並處理 401 錯誤。  
- **程式碼示例** (`frontend/src/services/api.js`):

```javascript
import axios from 'axios';

const api = axios.create({
  baseURL: import.meta.env.VITE_APP_API_URL || 'http://localhost/api',
  headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
});

api.interceptors.request.use(config => {
  const token = localStorage.getItem('token');
  if (token) config.headers.Authorization = `Bearer ${token}`;
  return config;
});

api.interceptors.response.use(
  response => response,
  error => {
    if (error.response.status === 401) {
      localStorage.removeItem('token');
      console.log('Token expired or invalid. Redirecting to login...');
    }
    return Promise.reject(error);
  }
);

export default api;
```

### 8. 有哪些常見問題與解決方法？

**答**:  
- **端口衝突**: 修改 `docker-compose.yml` 的端口映射。  
- **Composer 安裝失敗**:  
   ```bash
   docker-compose exec php composer install
   ```
- **前端無法載入**:  
   ```bash
   docker-compose exec node npm run dev
   ```
- **權限問題**:  
   ```bash
   docker-compose exec php chmod -R 775 storage bootstrap/cache
   ```
- **API 401 錯誤**: 檢查 `localStorage` 的 token 或重新登入。

### 9. 專案有哪些亮點？

**答**:  
- **一鍵部署**: Docker Compose 自動建置所有服務。  
- **AI 整合能力**: 預留 RAG（Pinecone/ChromaDB）與 LLM（OpenAI/Azure）接口，Laravel 輕鬆串接。  
- **模組化設計**: 路由拆分（`routes/api/`）、Composables 提高可維護性。  
- **即時聊天**: Redis 與 Event Broadcaster 實現訊息推播。  
- **安全保障**: Sanctum 權杖驗證、CORS 支援、Axios 攔截器。

### 10. 如何擴展系統功能？

**答**:  
- **AI 增強**: 整合 OpenAI、Pinecone，實現真實 RAG 與 LLM 回覆。  
- **WebSocket**: 使用 Laravel Echo 與 Pusher 增強即時聊天。  
- **文件解析**: 整合 Apache Tika 解析文件。  
- **多語言**: 前端加入 i18n，後端支援語言欄位。  
- **效能**: Redis 快取 API 結果，分片上傳大型文件。

---

## 目錄結構

- **backend/**: Laravel 應用（Controllers、Services、Routes）。  
- **frontend/**: Vue3 應用（Views、Services、Composables）。  
- **docker/**: Docker 配置（Nginx、PHP Dockerfile）。  
- **public/**: 靜態資源。  
- **docs/**: 專案文件。

---

## 文件與資源

- **專案文件**: `docs/README.md`  
- **公開資源**: `public/README.md`  
- **Docker 配置**: `docker/README.md`  

如有問題，請參考 `docs/` 或聯繫維護者。
