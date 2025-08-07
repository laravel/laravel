<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\InstallerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CreditPackageController;
use App\Http\Controllers\BlogPostController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ThreadController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/install', [InstallerController::class, 'index'])->name('install');
Route::post('/install', [InstallerController::class, 'store'])->name('install.store');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth','admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::view('/', 'admin.index')->name('index');
    Route::resource('agents', AgentController::class);
    Route::resource('packages', CreditPackageController::class)->except(['show']);
    Route::resource('blog', BlogPostController::class);
    Route::resource('pages', PageController::class);
});

Route::middleware('auth')->group(function () {
    Route::post('/checkout', [PaymentController::class, 'checkout'])->name('checkout');
    Route::get('/threads/{thread}/share', [ThreadController::class, 'share'])->name('threads.share');
});

Route::get('/t/{thread}', [ThreadController::class, 'show'])->name('threads.show');

Route::post('/stripe/webhook', [PaymentController::class, 'webhook'])->name('stripe.webhook');

Route::get('/bots/{slug}', [AgentController::class, 'showPublic'])->name('agents.public');
Route::get('/embed/{slug}', [AgentController::class, 'embed'])->name('agents.embed');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
