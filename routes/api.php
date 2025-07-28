<?php

use App\Http\Controllers\BoardController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\StatusController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');

Route::apiResource('projects', ProjectController::class);
Route::apiResource('boards', BoardController::class);
Route::apiResource('statuses', StatusController::class);
Route::apiResource('cards', CardController::class);
