# AI 虛擬人互動管理系統

本專案是一個基於 **Laravel** 後端 API 和 **Vue3 + Vite** 前端的 AI 虛擬人互動管理系統，透過 **Docker Compose** 實現一鍵部署，提供完整的開發環境與功能模組。本文件詳細介紹系統架構、核心功能、亮點與關鍵程式碼，幫助開發者快速上手並進行功能擴展。

---

## 專案目標

1. **一鍵部署開發環境**  
   使用 Docker Compose 自動建置 Nginx、PHP-FPM、MySQL、Redis 與 Node.js 容器，簡化環境配置，實現「秒上手」。

2. **完整互動管理平台**  
   提供使用者註冊/登入、AI 虛擬人角色管理、知識庫文件上傳、即時聊天室與儀表板統計功能。

3. **模組化與可擴充設計**  
   - 後端採用 Laravel 分層架構（Controllers、Services、Requests、Events、Policies），易於維護與擴展。  
   - 前端使用 Vue3 + Vite，透過 Composables、Router 與 Services 實現模組化開發。  
   - 預留 AI 模型整合接口（如 RAG 與 LLM 串接）。

---

## 系統架構

系統採用現代化 Web 開發架構，透過 Docker 容器化實現一致性與可擴充性。以下為架構圖與說明：

```
[Browser (Vue3)] <--- HTTP/WS ---> [Nginx]
                             │
                   ┌─────────┴─────────┐
                   │                   │
            [PHP-FPM Container]   [Node Container]
                   │                   │
          Laravel API Endpoints      Vite Dev Server
                   │
    ┌──────────────┴───────────────┐
    │        MySQL (8.0)           │
    │        Redis (Alpine)        │
    └──────────────────────────────┘
```

### 1. 部署層 (Docker)
- **Docker Compose**: 透過 `docker-compose.yml` 協調多服務容器（Nginx、PHP、MySQL、Redis、Node.js）。  
- **Nginx**: 反向代理，處理 HTTP 請求並路由至 PHP-FPM。  
  - 配置文件: `docker/nginx/default.conf`  
- **PHP (Laravel-FPM)**: 後端核心，運行 Laravel 應用，處理業務邏輯與 API 請求。  
  - 自訂映像: `docker/php/Dockerfile`（安裝 PHP 擴充與 Composer）。  
- **MySQL**: 關聯式資料庫，儲存使用者、虛擬人設定、對話與知識庫文件。  
- **Redis**: 用於快取、Session 管理與事件廣播。  
- **Node.js**: 提供 Vite 開發伺服器，運行 Vue3 前端應用。  

### 2. 後端 (Laravel API)
- **目錄結構**:
  - `Controllers`: 處理 API 請求邏輯（如 AuthController、CharacterController）。  
  - `Middleware`: 自訂 CORS 跨域處理。  
  - `Requests`: 表單驗證（如 CharacterRequest）。  
  - `Responses`: 統一 API 回應格式（ApiResponse）。  
  - `Models`: 資料表模型（User、Character、Document、Conversation、ChatMessage）。  
  - `Services`: 業務邏輯封裝（如 KnowledgeBaseService、ChatService）。  
  - `Events`: 事件廣播（如 ChatMessageSent）。  
  - `Policies`: 權限控制（如 CharacterPolicy）。  
  - `routes/api/`: 按模組拆分路由（auth、characters、knowledge-base、conversations、dashboard）。  

- **核心技術**:
  - **Laravel Sanctum**: 提供權杖驗證，確保 API 安全。  
  - **ApiResponse**: 統一成功/錯誤回應格式，簡化前端解析。  
  - **CORS 中介層**: 支援前後端分離跨域請求。  
  - **Redis 事件廣播**: 實現即時聊天訊息推播。  
  - **Gate 與 Policy**: 實現 Admin/User 角色權限控制。  

- **資料表設計**:
  - `users`: 儲存使用者資料（含角色欄位 `role`）。  
  - `characters`: 儲存 AI 虛擬人設定（名稱、描述、JSON 格式的 Persona）。  
  - `documents`: 儲存知識庫文件（含 user_id 外鍵）。  
  - `conversations`: 儲存對話紀錄（關聯 user_id 與 character_id）。  
  - `chat_messages`: 儲存聊天訊息（關聯 conversation_id）。  

### 3. 前端 (Vue3 + Vite)
- **目錄結構**:
  - `views/`: 核心頁面（HomeView、Auth、ConversationList、ChatRoom 等）。  
  - `views/Admin/`: 管理員專用頁面（AdminDashboard、UserManagement）。  
  - `components/`: 可重用組件（CharacterForm、DocumentUploader、MessageList 等）。  
  - `router/`: 路由管理，支援權限控制（requiresAuth、requiresAdmin）。  
  - `services/`: API 服務模組（api.js、chat.js）。  
  - `composables/`: Vue3 狀態管理（useAuth.js）。  
  - `assets/`: 靜態資源（如 main.css 整合 Tailwind CSS）。  

- **核心頁面**:
  - `HomeView.vue`: 首頁，引導使用者登入。  
  - `Auth.vue`: 註冊/登入頁面，支援表單切換。  
  - `AdminDashboard.vue`: 管理員儀表板，展示統計數據。  
  - `UserManagement.vue`: 管理員專用，使用者列表管理。  
  - `Characters.vue`: AI 虛擬人管理頁面，包含 CRUD 表單。  
  - `KnowledgeBase.vue`: 知識庫文件管理，支援上傳與展示。  
  - `ConversationList.vue`: 顯示使用者對話清單。  
  - `ChatRoom.vue`: 即時聊天室，支援訊息收發與顯示。  

- **API 服務**:
  - `api.js`: 配置 Axios 基礎 URL 與攔截器，自動附加 token 並處理 401 錯誤。  
  - `chat.js`: 提供對話與訊息的 API 存取（獲取對話、開始對話、收發訊息）。  

- **Composables**:
  - `useAuth.js`: 管理使用者狀態、檢查登入與角色，提供全站共用邏輯。  

---

## 環境準備

在開始之前，請確保已安裝以下工具：  
- **Docker Desktop**: 用於運行容器化服務。  
- **Git**: 用於版本控制與專案複製（若專案已托管）。  

---

## 安裝與執行

請按照以下步驟在本地環境中設置與運行專案：

1. **複製專案**  
   將專案資料夾複製到您的開發環境，或使用 Git 複製（若專案已托管於版本控制倉庫）。

2. **啟動所有服務**  
   在專案根目錄下執行以下指令，建置並啟動所有容器：  
   ```bash
   docker-compose up -d --build
   ```

3. **配置後端**  
   - 進入 PHP 容器並完成 Laravel 專案設置：  
     ```bash
     docker-compose exec php bash
     composer create-project laravel/laravel .
     composer install
     cp .env.example .env
     php artisan key:generate
     php artisan migrate
     exit
     ```

4. **安裝前端依賴**  
   進入 Node.js 容器並安裝前端套件：  
   ```bash
   docker-compose exec node bash
   npm install
   exit
   ```

5. **啟動前端開發伺服器**  
   啟動 Vite 開發伺服器，前端頁面將可透過 `http://localhost:5173` 訪問：  
   ```bash
   docker-compose exec node npm run dev
   ```

6. **訪問應用程式**  
   - **前端**: 開啟瀏覽器，訪問 `http://localhost:5173`。  
   - **後端 API**: Laravel API 可透過 `http://localhost/api` 訪問。  

---

## 核心模組與亮點

| 模組 | 說明 | 狀態 |
|------|------|------|
| **身份驗證** | 使用 Laravel Sanctum 實現註冊、登入、登出與權杖管理，支援 Admin/User 角色分層。 | 完成 |
| **AI 虛擬人** | 提供 CRUD 操作，允許管理員建立與管理 AI 虛擬人角色（含 JSON 格式 Persona）。 | 完成 |
| **知識庫管理** | 支援上傳文件（txt、pdf、docx）作為 AI 知識來源，預留向量化接口。 | 完成 |
| **對話與聊天** | 支援開始對話、存取歷史訊息與即時聊天，結合 Redis 事件廣播實現即時推播。 | 完成 |
| **儀表板** | 展示系統統計數據（用戶數、角色數、對話數等），僅管理員可見。 | 完成 |

### 亮點
1. **一鍵化專案骨架**  
   腳本自動生成所有資料夾、`.gitkeep`、`.env.example` 與 `README.md`，不到 1 分鐘即可獲得完整專案結構。

2. **模組化路由拆分**  
   後端路由按功能拆分至 `routes/api/`（如 `auth.php`、`characters.php`），提高可維護性。

3. **權限管理**  
   使用 Laravel Gate 與 Policy 實現精細的角色權限控制（如僅管理員可管理虛擬人）。

4. **即時聊天廣播**  
   透過 Redis 與 Laravel Event Broadcaster，實現聊天訊息的即時推播，前端可透過 WebSocket 訂閱。

5. **知識庫向量化預留**  
   `KnowledgeBaseService` 提供文件解析、內容分塊與向量嵌入的預留接口，方便整合 ChromaDB 或 Pinecone。

6. **前端 Composables**  
   `useAuth.js` 封裝使用者狀態與角色檢查邏輯，簡化全站身份驗證管理。

7. **安全與跨域防護**  
   - Laravel Sanctum 提供安全的權杖驗證。  
   - CORS 中介層支援前後端分離跨域請求。  
   - Axios 攔截器自動處理 token 與 401 錯誤。

8. **優化開發體驗**  
   - Vue3 + Vite 提供熱更新與快速編譯。  
   - Tailwind CSS 加速前端樣式開發。  
   - Redis 提升快取與事件處理效能。

---

## AI 模式串接位置

系統預留了以下 AI 相關功能接口，方便後續整合大語言模型（LLM）與向量資料庫：

1. **KnowledgeBaseService.php**  
   - 功能：處理文件上傳與刪除，預留向量化邏輯。  
   - 串接點：`uploadDocument` 方法中註解了文件解析、內容分塊與向量嵌入的步驟，可整合 ChromaDB 或 Pinecone。  
   - 程式碼（含中文註解）：

```php
<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class KnowledgeBaseService
{
    /**
     * 上傳文件並建立文件紀錄。
     * @param UploadedFile $file 上傳的文件
     * @param int $userId 上傳者的使用者 ID
     * @return Document 新建立的文件記錄
     */
    public function uploadDocument(UploadedFile $file, int $userId): Document
    {
        // 儲存文件到 public 磁碟的 knowledge_base 目錄
        $path = $file->store('knowledge_base', 'public');

        // 建立文件記錄
        $document = Document::create([
            'user_id' => $userId,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'status' => 'uploaded',
        ]);

        // TODO: 文件處理邏輯
        // 1. 解析文件內容為純文字 (可用 Tika 或 pdf2text)
        // 2. 將文字切分為塊 (chunks)，如每段 500 字
        // 3. 使用嵌入模型 (如 HuggingFace 或 OpenAI) 將塊轉為向量
        // 4. 儲存向量至向量資料庫 (如 ChromaDB 或 Pinecone)

        // 模擬處理完成，更新狀態
        $document->update(['status' => 'processed']);

        return $document;
    }

    /**
     * 刪除文件及文件記錄。
     * @param Document $document 要刪除的文件記錄
     * @return bool 是否刪除成功
     */
    public function deleteDocument(Document $document): bool
    {
        // 從儲存磁碟刪除文件
        Storage::disk('public')->delete($document->file_path);
        // 刪除資料庫記錄
        return $document->delete();
    }
}
```

2. **ChatService.php**  
   - 功能：生成 AI 回覆，模擬 RAG（檢索增強生成）流程。  
   - 串接點：`generateResponse` 方法中可替換為 OpenAI 或 Azure LLM API 呼叫。  
   - 程式碼（含中文註解）：

```php
<?php

namespace App\Services;

use App\Models\Conversation;
use Illuminate\Support\Facades\Log;

class ChatService
{
    /**
     * 模擬 RAG 流程，生成 AI 回覆。
     * @param Conversation $conversation 當前對話
     * @param string $userMessage 使用者輸入的訊息
     * @return string AI 生成的回覆
     */
    public function generateResponse(Conversation $conversation, string $userMessage): string
    {
        // 1. 檢索相關知識（待實作）
        // 可整合向量資料庫，根據 $userMessage 查詢相關文件塊
        // $relevantChunks = $this->vectorStoreService->retrieve($userMessage);

        // 2. 獲取角色 Persona
        $characterPersona = $conversation->character->persona;

        // 3. 獲取對話歷史
        $historyMessages = $conversation->messages()->orderBy('created_at')->get();
        $history = $historyMessages->map(fn($msg) => ['role' => $msg->sender, 'content' => $msg->content])->toArray();

        // 4. 拼接 Prompt
        $prompt = $this->buildPrompt($characterPersona, $history, $userMessage);

        // 5. 呼叫 LLM（模擬）
        // TODO: 替換為真實 LLM API 呼叫 (如 OpenAI 或 Azure)
        Log::info('模擬 LLM 呼叫', ['prompt' => $prompt]);

        // 模擬回覆，基於 Persona 與輸入訊息
        return "好的，我知道了。你剛才說：'{$userMessage}'。作為 {$characterPersona['name']}，我會這樣回覆你。";
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

        $prompt .= "User: " . $userMessage . "\n";
        $prompt .= "AI: ";

        return $prompt;
    }
}
```

3. **ChatMessageController.php**  
   - 功能：處理聊天訊息儲存與廣播，呼叫 ChatService 產生 AI 回覆。  
   - 串接點：`store` 方法中的 AI 回覆邏輯可與外部 LLM 服務整合。  
   - 程式碼（含中文註解）：

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

    /**
     * 注入 ChatService 依賴
     */
    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * 獲取指定對話的訊息列表
     * @param Conversation $conversation 對話實例
     * @return ApiResponse 包含訊息列表的回應
     */
    public function index(Conversation $conversation)
    {
        $this->authorize('view', $conversation);
        $messages = $conversation->messages()->orderBy('created_at', 'asc')->get();
        return ApiResponse::success($messages);
    }

    /**
     * 儲存新訊息並觸發 AI 回覆
     * @param Request $request HTTP 請求
     * @param Conversation $conversation 對話實例
     * @return ApiResponse 包含使用者訊息的回應
     */
    public function store(Request $request, Conversation $conversation)
    {
        $this->authorize('update', $conversation);
        $request->validate([
            'message' => 'required|string',
        ]);

        // 儲存使用者訊息
        $userMessage = $conversation->messages()->create([
            'sender' => 'user',
            'content' => $request->message,
        ]);

        // 廣播使用者訊息
        event(new ChatMessageSent($userMessage));

        // 生成 AI 回覆
        $aiResponse = $this->chatService->generateResponse($conversation, $request->message);

        // 儲存 AI 回覆
        $aiMessage = $conversation->messages()->create([
            'sender' => 'ai',
            'content' => $aiResponse,
        ]);

        // 廣播 AI 回覆
        event(new ChatMessageSent($aiMessage));

        return ApiResponse::success($userMessage, 'Message sent successfully', 201);
    }
}
```

---

## 安裝與配置注意事項

- **執行前檢查**：  
  確保 Docker Desktop 已運行，且本地端口 `80`、`3306`、`6379`、`5173` 未被佔用。  

- **環境變數**：  
  檢查 `backend/.env.example`，確保 `DB_HOST=mysql`、`REDIS_HOST=redis` 等配置正確。  

- **權限問題**：  
  若 Laravel 出現儲存權限錯誤，執行以下指令：  
  ```bash
  docker-compose exec php chmod -R 775 storage bootstrap/cache
  ```

- **生產環境建議**：  
  - 配置 HTTPS（Nginx）。  
  - 加密 `.env` 中的敏感資訊（如資料庫密碼）。  
  - 定期備份 MySQL 資料庫。  

- **停止服務**：  
  ```bash
  docker-compose down
  ```

---

## 問題排查

1. **端口衝突**  
   若 `80`、`3306`、`6379` 或 `5173` 端口被佔用，修改 `docker-compose.yml` 中的端口映射。  

2. **Composer 安裝失敗**  
   若 `composer install` 失敗，檢查 PHP 容器網路連線，或手動執行：  
   ```bash
   docker-compose exec php composer install
   ```

3. **前端無法載入**  
   確認 Node.js 容器運行正常，且 `npm install` 已成功執行。若 Vite 伺服器未啟動，重試：  
   ```bash
   docker-compose exec node npm run dev
   ```

4. **API 401 錯誤**  
   檢查 `localStorage` 是否包含有效 token，或重新登入以獲取新 token。

---

## 貢獻指南

歡迎為本專案貢獻程式碼！請按照以下步驟：  
1. 複製倉庫（若已托管）。  
2. 建立功能分支：`git checkout -b feature/your-feature`。  
3. 提交更改：`git commit -m "Add your feature"`。  
4. 推送分支：`git push origin feature/your-feature`。  
5. 開啟 Pull Request，詳細描述您的更改內容。  

---

## 未來擴展建議

1. **AI 模型整合**  
   - 整合 OpenAI 或 Azure LLM API，替換 `ChatService::generateResponse` 中的模擬回覆。  
   - 實現向量資料庫（如 ChromaDB）以支援 RAG 檢索。  

2. **WebSocket 即時聊天**  
   - 使用 Laravel Echo 與前端 Pusher 實現真正的即時訊息推播。  

3. **文件解析增強**  
   - 整合 Apache Tika 或 pdf2text 進行文件內容解析。  
   - 將解析後的文字分塊並嵌入向量資料庫。

4. **多語言支援**  
   - 在前端加入 i18n 套件，支援多語言切換。  
   - 後端 API 回應增加語言欄位。

5. **效能優化**  
   - 使用 Redis 快取頻繁查詢的 API 結果。  
   - 為大型文件上傳實現分片處理。

---

## 文件與資源

- **專案文件**: `docs/README.md`  
- **公開資源**: `public/README.md`  
- **Docker 配置**: `docker/README.md`  

如有其他問題，請參考 `docs/` 目錄中的詳細說明，或聯繫專案維護者。
