<?php

use App\Http\Controllers\CharacterController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('characters', CharacterController::class);
});
