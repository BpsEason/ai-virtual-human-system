# AI 虛擬人互動管理系統

這是一個使用 Laravel 作為後端 API、Vue.js 作為前端的 AI 虛擬人互動管理系統。

## 環境準備

在開始之前，請確保您的電腦已安裝 **Docker Desktop**。

1.  **複製專案到本地**

    將此專案資料夾複製到您的開發環境中。

2.  **啟動所有服務**

    在專案根目錄下，執行以下指令啟動 Nginx、PHP、MySQL 與 Node.js 服務。

    ```bash
    docker-compose up -d --build
    ```

3.  **進入容器並完成專案設定**

    使用以下指令進入 PHP 容器，並執行資料庫遷移。

    ```bash
    docker-compose exec php cp .env.example .env
    docker-compose exec php php artisan key:generate
    docker-compose exec php php artisan migrate
    ```

    接著，進入 Node.js 容器，安裝前端依賴。

    ```bash
    docker-compose exec node npm install
    ```

4.  **啟動前端開發伺服器**

    在 Node.js 容器中執行此指令，即可透過 http://localhost:5173 訪問前端頁面。

    ```bash
    docker-compose exec node npm run dev
    ```

---

## 核心模組概覽

| 模組 | 說明 | 狀態 |
| :--- | :--- | :--- |
| **身份驗證** | 使用 Laravel Sanctum 實現使用者註冊、登入、登出與權杖管理。 | 完成 |
| **AI 虛擬人** | 提供 CRUD 操作來建立和管理不同的 AI 虛擬人角色。 | 完成 |
| **知識庫管理** | 允許上傳文件（txt, pdf, docx），作為 AI 虛擬人的知識來源。 | 完成 |
| **聊天功能** | 實現使用者與 AI 虛擬人的即時聊天互動。 | 完成 |
| **儀表板** | 顯示系統的概覽統計數據。 | 完成 |
