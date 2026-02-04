<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Guru\DashboardController as GuruDashboard;
use App\Http\Controllers\Guru\MaterialController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboard;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Landing page
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    // Redirect authenticated users to dashboard
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('landing');
})->name('home');

/*
|--------------------------------------------------------------------------
| Dashboard redirect berdasarkan role
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    $role = auth()->user()?->role;
    
    return match($role) {
        'admin' => redirect()->route('admin.dashboard'),
        'guru' => redirect()->route('guru.dashboard'),
        'siswa' => redirect()->route('siswa.dashboard'),
        default => redirect('/'),
    };
})->middleware('auth')->name('dashboard');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users');
    Route::get('/users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/reports', [AdminDashboard::class, 'reports'])->name('reports');
});

/*
|--------------------------------------------------------------------------
| Guru Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/', [GuruDashboard::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [GuruDashboard::class, 'index'])->name('dashboard');
    Route::get('/materials', [MaterialController::class, 'index'])->name('materials');
    Route::post('/materials', [MaterialController::class, 'store'])->name('materials.store');
    Route::get('/materials/{material}/edit', [MaterialController::class, 'edit'])->name('materials.edit');
    Route::put('/materials/{material}', [MaterialController::class, 'update'])->name('materials.update');
    Route::delete('/materials/{material}', [MaterialController::class, 'destroy'])->name('materials.destroy');
    Route::get('/chat', [ChatController::class, 'index'])->name('chat');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
});

/*
|--------------------------------------------------------------------------
| Siswa Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/', [SiswaDashboard::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [SiswaDashboard::class, 'index'])->name('dashboard');
    Route::get('/materials', [SiswaDashboard::class, 'index'])->name('materials');
    Route::get('/materials/{material}/download', [SiswaDashboard::class, 'download'])->name('materials.download');
    Route::get('/chat', [ChatController::class, 'index'])->name('chat');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
});

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Chat routes (Messages)
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{user}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{user}', [MessageController::class, 'store'])->name('messages.store');
    
    // Report routes
    Route::resource('reports', \App\Http\Controllers\ReportController::class);
});

/*
|--------------------------------------------------------------------------
| Auth routes
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
