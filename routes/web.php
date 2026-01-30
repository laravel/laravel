<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AssociadoController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ActivityLogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home redirect
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Admin Routes (requires admin or editor role)
Route::middleware(['auth', 'role:admin,editor'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Associados CRUD
    Route::resource('associados', AssociadoController::class);

    // Posts Management
    Route::get('posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('posts/{post}', [PostController::class, 'show'])->name('posts.show');
    Route::get('posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::post('posts/{post}/publish', [PostController::class, 'publish'])->name('posts.publish');
    Route::post('posts/{post}/archive', [PostController::class, 'archive'])->name('posts.archive');
    Route::delete('posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    // Admin-only routes
    Route::middleware('role:admin')->group(function () {
        // User Management
        Route::resource('users', UserController::class)->except(['show']);

        // Activity Logs
        Route::get('logs', [ActivityLogController::class, 'index'])->name('logs.index');
        Route::get('logs/{log}', [ActivityLogController::class, 'show'])->name('logs.show');
    });
});

// Editor Routes
Route::middleware(['auth', 'role:editor'])->prefix('editor')->name('editor.')->group(function () {
    // Dashboard
    Route::get('/', [\App\Http\Controllers\Editor\DashboardController::class, 'index'])->name('dashboard');
    
    // Posts CRUD (own posts only)
    Route::get('posts', [\App\Http\Controllers\Editor\PostController::class, 'index'])->name('posts.index');
    Route::get('posts/create', [\App\Http\Controllers\Editor\PostController::class, 'create'])->name('posts.create');
    Route::post('posts', [\App\Http\Controllers\Editor\PostController::class, 'store'])->name('posts.store');
    Route::get('posts/{post}', [\App\Http\Controllers\Editor\PostController::class, 'show'])->name('posts.show');
    Route::get('posts/{post}/edit', [\App\Http\Controllers\Editor\PostController::class, 'edit'])->name('posts.edit');
    Route::put('posts/{post}', [\App\Http\Controllers\Editor\PostController::class, 'update'])->name('posts.update');
    Route::post('posts/{post}/publish', [\App\Http\Controllers\Editor\PostController::class, 'publish'])->name('posts.publish');
    Route::post('posts/{post}/unpublish', [\App\Http\Controllers\Editor\PostController::class, 'unpublish'])->name('posts.unpublish');
    Route::delete('posts/{post}', [\App\Http\Controllers\Editor\PostController::class, 'destroy'])->name('posts.destroy');
    
    // Profile
    Route::get('profile', [\App\Http\Controllers\Editor\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [\App\Http\Controllers\Editor\ProfileController::class, 'update'])->name('profile.update');
});
