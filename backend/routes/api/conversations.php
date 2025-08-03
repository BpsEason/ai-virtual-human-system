<?php

use App\Http\Controllers\ConversationController;
use App\Http\Controllers\ChatMessageController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('conversations', ConversationController::class)->only(['index', 'show']);
    Route::post('conversations/start', [ConversationController::class, 'start']);
    Route::get('conversations/{conversation}/messages', [ChatMessageController::class, 'index']);
    Route::post('conversations/{conversation}/messages', [ChatMessageController::class, 'store']);
});
