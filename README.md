# AI 虛擬人互動管理系統

本專案是一個基於 **Laravel** 後端 API 和 **Vue3 + Vite** 前端的 AI 虛擬人互動管理系統，透過 **Docker Compose** 實現一鍵部署，提供完整的開發環境與功能模組。本文件詳細介紹系統架構、核心功能、亮點與關鍵程式碼，幫助開發者快速上手並進行功能擴展。

---

## 專案目標

1. **一鍵部署開發環境**  
   使用 Docker Compose 自動建置 Nginx、PHP-FPM、MySQL、Redis 與 Node.js 容器，簡化環境配置，實現快速啟動。

2. **完整互動管理平台**  
   提供使用者註冊/登入、AI 虛擬人角色管理、知識庫文件上傳、即時聊天室與儀表板統計功能。

3. **模組化與可擴充設計**  
   - 後端採用 Laravel 分層架構（Controllers、Services、Requests、Events、Policies）。  
   - 前端使用 Vue3 + Vite，透過 Composables、Router 與 Services 實現模組化。  
   - 預留 AI 模型整合接口（如 RAG 與 LLM）。

---

## 系統架構

### 架構概覽

系統採用前後端分離架構，透過 Docker 容器化實現一致的開發與部署環境。以下是簡化的架構圖，展示各層的互動與技術選擇：

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

### 架構層次詳解

1. **前端層 (Vue3 + Vite)**  
   - **職責**: 提供單頁應用 (SPA) 使用者介面，處理使用者互動、呈現資料並與後端 API 通信。  
   - **技術**:  
     - **Vue3**: 負責組件化 UI 與響應式資料綁定。  
     - **Vite**: 提供快速編譯與熱模組重載 (HMR)，提升開發體驗。  
     - **Axios**: 封裝 API 請求，自動附加 Sanctum 權杖並處理錯誤。  
     - **Tailwind CSS**: 快速實現響應式樣式。  
   - **核心模組**:  
     - 頁面 (Views): Home、Auth、AdminDashboard、Characters、KnowledgeBase、ChatRoom 等。  
     - 服務 (Services): `api.js` (通用 API 請求)、`chat.js` (聊天相關 API)。  
     - 可組合函數 (Composables): `useAuth.js` 管理使用者狀態與權限。  

2. **Web 伺服器層 (Nginx)**  
   - **職責**: 作為反向代理，處理 HTTP 請求，將動態請求路由至 PHP-FPM，靜態資源直接回應。  
   - **技術**:  
     - **Nginx**: 高性能 Web 伺服器，配置於 `docker/nginx/default.conf`。  
   - **功能**:  
     - 處理前端 SPA 的靜態檔案（`public/` 目錄）。  
     - 將 `/api` 請求轉發至 PHP-FPM。  
     - 支援 CORS 跨域請求。

3. **後端層 (Laravel API)**  
   - **職責**: 提供 RESTful API，處理業務邏輯、資料庫操作與權限控制，支援即時事件廣播。  
   - **技術**:  
     - **Laravel 8.x**: 提供 MVC 架構與 Sanctum 權杖驗證。  
     - **PHP-FPM**: 運行 Laravel 應用，配置於 `docker/php/Dockerfile`。  
     - **Redis**: 用於快取、Session 管理與事件廣播。  
   - **核心模組**:  
     - **Controllers**: 處理 API 請求（如 AuthController、ChatMessageController）。  
     - **Services**: 封裝業務邏輯（如 KnowledgeBaseService、ChatService）。  
     - **Events**: 實現即時聊天廣播（如 ChatMessageSent）。  
     - **Policies**: 控制角色權限（如僅管理員可管理虛擬人）。  
     - **Routes**: 按模組拆分至 `routes/api/`（如 `auth.php`、`characters.php`）。  

4. **資料層 (MySQL + Redis)**  
   - **MySQL**:  
     - **職責**: 儲存結構化資料，包括使用者、虛擬人、文件與對話紀錄。  
     - **資料表**:  
       - `users`: 使用者資料（含角色欄位）。  
       - `characters`: AI 虛擬人設定（含 JSON Persona）。  
       - `documents`: 知識庫文件（含 user_id 外鍵）。  
       - `conversations`: 對話紀錄（關聯 user_id、character_id）。  
       - `chat_messages`: 聊天訊息（關聯 conversation_id）。  
   - **Redis**:  
     - **職責**: 處理快取、Session 與即時事件廣播。  
     - **功能**:  
       - 儲存使用者 Session。  
       - 支援 Laravel Event Broadcaster 的即時訊息推播。  

5. **容器化層 (Docker)**  
   - **職責**: 將所有服務封裝為容器，確保環境一致性與可移植性。  
   - **技術**:  
     - **Docker Compose**: 定義並協調多容器服務（`docker-compose.yml`）。  
     - **自訂映像**:  
       - `docker/php/Dockerfile`: 安裝 PHP 擴充與 Composer。  
       - `docker/nginx/default.conf`: 配置 Nginx 路由。  
   - **服務**:  
     - Nginx (port 80)  
     - PHP-FPM (port 9000)  
     - MySQL (port 3306)  
     - Redis (port 6379)  
     - Node.js (port 5173, Vite 開發伺服器)  

---

## 環境準備

在開始之前，請確保已安裝以下工具：  
- **Docker Desktop**: 用於運行容器化服務。  
- **Git**: 用於版本控制與專案複製（若專案已托管）。  

---

## 安裝與執行

請按照以下步驟在本地環境中設置與運行專案：

1. **複製專案**  
   將專案資料夾複製到您的開發環境，或使用 Git 複製（若專案已托管）。  
   ```bash
   git clone <repository-url>
   cd ai-virtual-human-system
   ```

2. **啟動所有服務**  
   在專案根目錄下執行以下指令，建置並啟動所有容器：  
   ```bash
   docker-compose up -d --build
   ```

3. **配置後端**  
   進入 PHP 容器並完成 Laravel 設置：  
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
| **身份驗證** | 使用 Laravel Sanctum 實現註冊、登入、登出與權杖管理，支援 Admin/User 角色。 | 完成 |
| **AI 虛擬人** | 提供 CRUD 操作，允許管理員建立與管理 AI 虛擬人角色（含 JSON 格式 Persona）。 | 完成 |
| **知識庫管理** | 支援上傳文件（txt、pdf、docx）作為 AI 知識來源，預留向量化接口。 | 完成 |
| **對話與聊天** | 支援開始對話、存取歷史訊息與即時聊天，結合 Redis 事件廣播。 | 完成 |
| **儀表板** | 展示系統統計數據（用戶數、角色數、對話數等），僅管理員可見。 | 完成 |

### 亮點
1. **一鍵化專案骨架**  
   腳本自動生成目錄結構、`.gitkeep`、`.env.example` 與 `README.md`，不到 1 分鐘完成專案初始化。

2. **模組化路由拆分**  
   後端路由按功能拆分至 `routes/api/`（如 `auth.php`、`characters.php`），提高可維護性。

3. **權限管理**  
   使用 Laravel Gate 與 Policy 實現 Admin/User 角色控制，例如僅管理員可管理虛擬人。

4. **即時聊天廣播**  
   透過 Redis 與 Laravel Event Broadcaster，實現聊天訊息的即時推播，前端可透過 WebSocket 訂閱。

5. **知識庫向量化預留**  
   `KnowledgeBaseService` 提供文件解析、內容分塊與向量嵌入接口，方便整合 ChromaDB 或 Pinecone。

6. **前端 Composables**  
   `useAuth.js` 封裝使用者狀態與角色檢查，簡化全站身份驗證管理。

7. **安全與跨域防護**  
   - Laravel Sanctum 提供權杖驗證。  
   - CORS 中介層支援前後端分離。  
   - Axios 攔截器自動處理 token 與 401 錯誤。

8. **優化開發體驗**  
   - Vue3 + Vite 提供熱更新與快速編譯。  
   - Tailwind CSS 加速樣式開發。  
   - Redis 提升快取與事件效能。

---

## AI 模式串接位置

系統預留了以下 AI 相關功能接口，方便整合大語言模型（LLM）與向量資料庫：

1. **KnowledgeBaseService.php**  
   - **功能**: 處理文件上傳與刪除，預留向量化邏輯。  
   - **串接點**: `uploadDocument` 方法中可加入文件解析與向量嵌入邏輯。  
   - **程式碼（含中文註解）**:

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
     * @param UploadedFile
