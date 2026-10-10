<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\AuthController;

// Route publik
Route::post('/login', [AuthController::class, 'login']);

// Route yang dilindungi Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);
});

// Route CRUD Books
Route::apiResource('books', BookController::class);