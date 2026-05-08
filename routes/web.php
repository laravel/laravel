<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\NotificationController;

// ── Auth ──────────────────────────────────────────────────────────────────────
Route::get('/',          [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',    [AuthController::class, 'login'])->name('login.post');
Route::post('/logout',   [AuthController::class, 'logout'])->name('logout');
Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

// ── Authenticated ─────────────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // ── Customer ──────────────────────────────────────────────────────────────

    Route::get('/shop', [ShopController::class, 'index'])->name('customer.shop');

    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('customer.announcements');

    Route::get('/cart',               [CartController::class, 'index'])->name('customer.cart');
    Route::post('/cart/add',          [CartController::class, 'addOrUpdate'])->name('customer.cart.add');
    Route::put('/cart/{cartItem}',    [CartController::class, 'update'])->name('customer.cart.update');
    Route::delete('/cart/{cartItem}', [CartController::class, 'remove'])->name('customer.cart.remove');
    Route::delete('/cart',            [CartController::class, 'clear'])->name('customer.cart.clear');
    Route::get('/cart/count',         [CartController::class, 'count'])->name('customer.cart.count');

    Route::get('/orders',            [OrderController::class, 'index'])->name('customer.orders');
    Route::post('/orders',           [OrderController::class, 'store'])->name('customer.orders.store');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('customer.orders.destroy');

    Route::get('/history',            [HistoryController::class, 'index'])->name('customer.history');
    Route::delete('/history/{order}', [HistoryController::class, 'destroy'])->name('customer.history.destroy');

    Route::get('/notifications',           [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
    Route::get('/notifications/count',     [NotificationController::class, 'count'])->name('notifications.count');

    // ── Admin ─────────────────────────────────────────────────────────────────
    Route::prefix('admin')->name('admin.')->group(function () {

        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        Route::get('/users',         [AdminController::class, 'users'])->name('users');
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');

        // ✅ FIX: Explicit routes lang — walang Route::resource para walang conflict
        // Ang prefix('admin') ay nag-aadد ng /admin sa lahat, kaya /products lang ang isusulat
        Route::get('/products',              [AdminProductController::class, 'index'])->name('products');
        Route::post('/products',             [AdminProductController::class, 'store'])->name('products.store');
        Route::put('/products/{product}',    [AdminProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');

        Route::get('/orders',                  [AdminOrderController::class, 'index'])->name('orders');
        Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
        Route::patch('/orders/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('orders.cancel');

        Route::get('/history', [AdminOrderController::class, 'history'])->name('history');

        Route::get('/financial', [\App\Http\Controllers\FinancialController::class, 'index'])->name('financial');

        Route::get('/dashboard/data',  [AdminController::class, 'dashboardData'])->name('dashboard.data');
        Route::get('/dashboard/check', [AdminController::class, 'dashboardCheck'])->name('dashboard.check');

        // Announcements
        Route::get('/announcements',                          [AnnouncementController::class, 'adminIndex'])->name('announcements');
Route::post('/announcements',                         [AnnouncementController::class, 'store'])->name('announcements.store');
Route::put('/announcements/{announcement}',           [AnnouncementController::class, 'update'])->name('announcements.update');
Route::patch('/announcements/{announcement}/toggle',  [AnnouncementController::class, 'toggleActive'])->name('announcements.toggle');
Route::delete('/announcements/{announcement}',        [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
    });
});