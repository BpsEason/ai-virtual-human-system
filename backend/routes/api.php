<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CharacterController;
use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KnowledgeBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    Route::apiResource('characters', CharacterController::class);
    Route::apiResource('knowledge-base', KnowledgeBaseController::class)->only(['index', 'destroy']);
    Route::post('knowledge-base/upload', [KnowledgeBaseController::class, 'upload']);
    Route::apiResource('chat-messages', ChatMessageController::class)->only(['index', 'store']);
    Route::get('dashboard/stats', [DashboardController::class, 'stats']);
});
