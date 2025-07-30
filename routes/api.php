<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\StatusController;

use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // RESTful endpoints
    Route::apiResource('projects', ProjectController::class);
    Route::apiResource('boards', BoardController::class);
    Route::apiResource('statuses', StatusController::class);
    Route::apiResource('cards', CardController::class);

    // Auth-only routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
