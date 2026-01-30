<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AssociadoController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ActivityLogController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Using session-based authentication for SPA (stateful)
| Session middleware is added in bootstrap/app.php
*/

// Public API routes (no auth required)
Route::prefix('public')->group(function () {
    Route::get('/associados', [AssociadoController::class, 'publicList']);
    Route::get('/posts', [PostController::class, 'publicList']);
    Route::get('/posts/{slug}', [PostController::class, 'showBySlug']);
});

// Auth routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');
Route::get('/user', [AuthController::class, 'user'])->middleware('auth');

// Protected routes (require authentication via session)
Route::middleware('auth')->group(function () {
    
    // Admin & Editor routes
    Route::prefix('admin')->middleware('role:admin,editor')->group(function () {
        // Dashboard stats
        Route::get('/stats', [AuthController::class, 'stats']);
        
        // Associados CRUD
        Route::get('/associados', [AssociadoController::class, 'index']);
        Route::post('/associados', [AssociadoController::class, 'store']);
        Route::get('/associados/{associado}', [AssociadoController::class, 'show']);
        Route::put('/associados/{associado}', [AssociadoController::class, 'update']);
        Route::delete('/associados/{associado}', [AssociadoController::class, 'destroy']);
        
        // Posts management
        Route::get('/posts', [PostController::class, 'index']);
        Route::get('/posts/{post}', [PostController::class, 'show']);
        Route::put('/posts/{post}', [PostController::class, 'update']);
        Route::post('/posts/{post}/publish', [PostController::class, 'publish']);
        Route::post('/posts/{post}/archive', [PostController::class, 'archive']);
        Route::delete('/posts/{post}', [PostController::class, 'destroy']);
    });
    
    // Admin-only routes
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        // User management
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
        
        // Activity logs
        Route::get('/logs', [ActivityLogController::class, 'index']);
        Route::get('/logs/{log}', [ActivityLogController::class, 'show']);
    });
    
    // Editor routes
    Route::prefix('editor')->middleware('role:editor')->group(function () {
        // Own posts CRUD
        Route::get('/posts', [PostController::class, 'myPosts']);
        Route::post('/posts', [PostController::class, 'store']);
        Route::get('/posts/{post}', [PostController::class, 'show']);
        Route::put('/posts/{post}', [PostController::class, 'update']);
        Route::post('/posts/{post}/publish', [PostController::class, 'publish']);
        Route::post('/posts/{post}/unpublish', [PostController::class, 'unpublish']);
        Route::delete('/posts/{post}', [PostController::class, 'destroy']);
        
        // Profile
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
    });
});

