<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class KnowledgeBaseService
{
    /**
     * 上傳文件並建立文件紀錄。
     */
    public function uploadDocument(UploadedFile $file, int $userId): Document
    {
        $path = $file->store('knowledge_base', 'public');

        $document = Document::create([
            'user_id' => $userId,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'status' => 'uploaded',
        ]);

        // 這裡可以加入處理文件內容的邏輯，例如：
        // 1. 將文件內容解析為純文字
        // 2. 將純文字切分成塊 (chunks)
        // 3. 將每個塊轉換為向量嵌入 (vector embeddings)
        // 4. 儲存這些向量到向量資料庫中 (如 ChromaDB, Pinecone 等)
        // 這部分需要整合外部服務，在此僅為範例

        // 假設處理成功，更新狀態
        $document->update(['status' => 'processed']);

        return $document;
    }

    /**
     * 刪除文件及文件紀錄。
     */
    public function deleteDocument(Document $document): bool
    {
        Storage::disk('public')->delete($document->file_path);
        return $document->delete();
    }
}
