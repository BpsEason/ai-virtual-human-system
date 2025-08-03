<?php

namespace App\Http\Controllers;

use App\Http\Requests\KnowledgeUploadRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Document;
use App\Services\KnowledgeBaseService;
use Illuminate\Http\Request;

class KnowledgeBaseController extends Controller
{
    protected $service;

    public function __construct(KnowledgeBaseService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $documents = Document::all();
        return ApiResponse::success($documents);
    }

    public function upload(KnowledgeUploadRequest $request)
    {
        $file = $request->file('document');
        $document = $this->service->uploadDocument($file);
        return ApiResponse::success($document, 'Document uploaded successfully', 201);
    }

    public function destroy(Document $document)
    {
        $this->service->deleteDocument($document);
        return ApiResponse::success(null, 'Document deleted successfully');
    }
}
