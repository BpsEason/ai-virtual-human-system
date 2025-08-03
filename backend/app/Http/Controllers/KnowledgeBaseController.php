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

    public function index(Request $request)
    {
        $documents = $request->user()->isAdmin()
            ? Document::all()
            : Document::where('user_id', $request->user()->id)->get();
        return ApiResponse::success($documents);
    }

    public function upload(KnowledgeUploadRequest $request)
    {
        $file = $request->file('document');
        $document = $this->service->uploadDocument($file, $request->user()->id);
        return ApiResponse::success($document, 'Document uploaded successfully', 201);
    }

    public function destroy(Document $document)
    {
        $this->authorize('delete', $document);
        $this->service->deleteDocument($document);
        return ApiResponse::success(null, 'Document deleted successfully');
    }
}
