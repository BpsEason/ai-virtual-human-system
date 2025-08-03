<?php

use App\Http\Controllers\KnowledgeBaseController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('knowledge-base', KnowledgeBaseController::class)->only(['index', 'destroy']);
    Route::post('knowledge-base/upload', [KnowledgeBaseController::class, 'upload']);
});
