# AI 虛擬人互動管理系統

本專案是一個使用 Laravel 作為後端 API、Vue.js (Vue3 + Vite) 作為前端的 AI 虛擬人互動管理系統，透過 Docker 容器化實現快速部署與開發。

## 專案目標

1. **快速一鍵部署**  
   透過 Docker Compose 自動建置 Nginx、PHP-FPM、MySQL 與 Node.js 容器，讓開發環境即刻啟動，無需繁瑣配置。

2. **完整互動管理平台**  
   提供使用者註冊/登入、AI 虛擬人角色 CRUD、知識庫文件上傳與管理、即時聊天室功能，以及儀表板統計展示。

3. **模組化與可擴充**  
   - 後端採用 Laravel 分層結構（Controllers、Services、Requests、Events 等），易於維護與擴充。  
   - 前端使用 Vue3 + Vite，組織清晰（Views、Router、Services），支援快速功能迭代。

## 系統架構

系統採用現代化 Web 開發架構，透過 Docker 容器化實現一致性與可擴充性。以下為架構概覽：

### 1. 部署層 (Docker)
- **Docker Compose**: 透過 `docker-compose.yml` 協調多服務容器（Nginx、PHP、MySQL、Node.js）。  
- **Nginx**: 反向代理，負責處理 HTTP 請求並路由至 PHP 應用。  
- **PHP (Laravel-FPM)**: 後端核心，處理業務邏輯與 API 請求。  
- **MySQL**: 關聯式資料庫，儲存使用者、虛擬人設定、聊天記錄與知識庫文件。  
- **Node.js**: 提供前端開發環境，運行 Vite 開發伺服器。  
- **自訂映像**:
  - `docker/php/Dockerfile`: 安裝 PHP 擴充套件與 Composer。  
  - `docker/nginx/default.conf`: 配置 Nginx 路由與 PHP FastCGI 處理。

### 2. 後端 (Laravel API)
- **目錄結構**:
  - Controllers: 處理 API 請求邏輯。
  - Middleware: 自訂 CORS 跨域處理。
  - Requests: 表單驗證規則。
  - Responses: 統一 API 回應格式（ApiResponse）。
  - Models: 資料表模型（User、Character、Document、ChatMessage）。
  - Services: 業務邏輯封裝（如 KnowledgeBaseService）。
  - Events: 事件廣播（如 ChatMessageSent）。
  - Exceptions: 自訂錯誤處理。
- **核心技術**:
  - **Laravel Sanctum**: 提供權杖驗證，確保 API 安全。
  - **ApiResponse**: 統一成功/錯誤回應格式，簡化前端解析。
  - **CORS 中介層**: 支援前後端分離跨域請求。
  - **ChatMessageSent 事件**: 實現即時聊天訊息廣播。
- **資料表設計**:
  - `users`: 儲存使用者資料（註冊、登入）。
  - `characters`: 儲存 AI 虛擬人設定（名稱、描述、角色特性）。
  - `documents`: 儲存知識庫文件（檔名、路徑、狀態）。
  - `chat_messages`: 儲存聊天記錄（角色 ID、發送者、訊息內容）。

### 3. 前端 (Vue3 + Vite)
- **目錄結構**:
  - `views`: 核心頁面（HomeView、Auth、Dashboard、Characters、KnowledgeBase、ChatRoom）。
  - `components`: 可重用組件。
  - `router`: 路由管理（支援權限控制）。
  - `services`: API 服務模組（api.js、chat.js）。
  - `assets`: 靜態資源（如 main.css）。
- **核心頁面**:
  - `HomeView.vue`: 首頁，引導使用者登入。
  - `Auth.vue`: 註冊/登入頁面，支援切換表單。
  - `Dashboard.vue`: 儀表板，顯示系統統計數據。
  - `Characters.vue`: AI 虛擬人管理頁面。
  - `KnowledgeBase.vue`: 知識庫文件管理頁面。
  - `ChatRoom.vue`: 即時聊天室頁面。
- **API 服務**:
  - `api.js`: 配置 Axios 基礎 URL 與授權攔截器，自動附加 token。
  - `chat.js`: 提供聊天訊息的獲取與發送功能。

## 環境準備

在開始之前，請確保已安裝以下工具：  
- **Docker Desktop**（用於容器化服務）。  
- **Git**（用於版本控制與專案複製）。

## 安裝與執行

請按照以下步驟在本地環境中設置與運行專案：

1. **複製專案**  
   將專案資料夾複製到您的開發環境，或使用 Git 複製（若專案已托管於版本控制倉庫）。

2. **啟動所有服務**  
   在專案根目錄下執行以下指令，建置並啟動所有容器（Nginx、PHP、MySQL、Node.js）：  
   ```bash
   docker-compose up -d --build
   ```

3. **配置後端**  
   - 複製 `.env.example` 為 `.env` 並生成應用程式金鑰：  
     ```bash
     docker-compose exec php cp .env.example .env
     docker-compose exec php php artisan key:generate
     ```
   - 執行資料庫遷移以建立資料表：  
     ```bash
     docker-compose exec php php artisan migrate
     ```

4. **安裝前端依賴**  
   進入 Node.js 容器並安裝前端套件：  
   ```bash
   docker-compose exec node npm install
   ```

5. **啟動前端開發伺服器**  
   啟動 Vite 開發伺服器，前端頁面將可透過 `http://localhost:5173` 訪問：  
   ```bash
   docker-compose exec node npm run dev
   ```

6. **訪問應用程式**  
   - **前端**: 開啟瀏覽器，訪問 `http://localhost:5173`。  
   - **後端 API**: Laravel API 可透過 `http://localhost/api` 訪問。

## 核心模組

| 模組 | 說明 | 狀態 |
|------|------|------|
| **身份驗證** | 使用 Laravel Sanctum 實現註冊、登入、登出與權杖管理，支援安全 API 存取。 | 完成 |
| **AI 虛擬人** | 提供 CRUD 操作，允許建立、編輯與管理 AI 虛擬人角色。 | 完成 |
| **知識庫管理** | 支援上傳與管理文件（txt、pdf、docx），作為 AI 知識來源，預留向量化接口。 | 完成 |
| **聊天功能** | 透過事件廣播實現使用者與 AI 虛擬人的即時聊天互動。 | 完成 |
| **儀表板** | 展示系統統計數據，如使用者數、角色數與文件數。 | 完成 |

## 專案亮點

1. **一鍵化專案骨架**  
   自動生成所有資料夾、`.gitkeep`、`.env.example` 與 `README.md`，從零開始不到 1 分鐘即可獲得完整專案結構。

2. **統一回應格式**  
   使用 `ApiResponse` 類別（success/error）確保所有 API 輸出格式一致，前端只需單一解析邏輯。

3. **即時聊天廣播**  
   結合 Laravel Event Broadcaster 與前端訂閱，實現聊天訊息的即時推播功能。

4. **知識庫向量化延伸**  
   `KnowledgeBaseService` 預留文件解析、內容分塊與向量嵌入接口，方便後續整合 ChromaDB 或 Pinecone 等向量資料庫。

5. **安全與跨域防護**  
   使用 Laravel Sanctum 進行權杖驗證，搭配 CORS 中介層，確保 API 安全並支援前後端分離開發。

6. **優化開發體驗**  
   - Vue3 + Vite 提供熱更新與快速編譯，提升前端開發效率。  
   - Axios 攔截器自動附加授權 token，簡化 API 請求處理。

## 目錄結構

- **backend/**: Laravel 後端應用（控制器、模型、遷移等）。  
- **frontend/**: Vue.js 前端應用（視圖、組件、路由等）。  
- **docker/**: Docker 配置文件（Nginx、PHP 等）。  
- **public/**: Laravel 公開目錄，處理靜態資源。  
- **docs/**: 存放文件資料（若有）。  

## 注意事項

- 執行 `docker-compose` 指令前，請確保 Docker Desktop 已運行。  
- `.env` 文件包含環境特定配置（如資料庫憑證、API URL），請根據需要調整以適應生產環境。  
- 生產環境建議採取額外安全措施，如啟用 HTTPS、加密環境變數與定期備份資料庫。  
- 停止服務：  
  ```bash
  docker-compose down
  ```

## 問題排查

- **端口衝突**：若 `80`、`3306` 或 `5173` 端口被佔用，請修改 `docker-compose.yml` 使用其他端口。  
- **權限問題**：確保 Laravel 的 `storage` 與 `bootstrap/cache` 目錄具有正確權限：  
  ```bash
  docker-compose exec php chmod -R 775 storage bootstrap/cache
  ```  
- **前端無法載入**：確認 Node.js 服務運行正常，且 `npm install` 已成功執行。

## 貢獻指南

歡迎為本專案貢獻程式碼！請按照以下步驟操作：  
1. 複製倉庫（若已托管）。  
2. 建立功能分支（`git checkout -b feature/your-feature`）。  
3. 提交更改（`git commit -m "Add your feature"`）。  
4. 推送分支（`git push origin feature/your-feature`）。  
5. 開啟 Pull Request，詳細描述您的更改內容。
