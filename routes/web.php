<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MenuItemController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/menuitems/create', [MenuItemController::class, 'create'])->name('menuitems.create');
    Route::post('/menuitems', [MenuItemController::class, 'store'])->name('menuitems.store');
    Route::post('/categories', [\App\Http\Controllers\CategoryController::class, 'store'])->name('categories.store');
    Route::get('/restaurants/create', [RestaurantController::class, 'create'])->name('restaurants.create');
    Route::post('/restaurants', [RestaurantController::class, 'store'])->name('restaurants.store');
});

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurants.index');
Route::get('/restaurants/{restaurant}', [RestaurantController::class, 'show'])->name('restaurants.show');
Route::get('/menuitems/{menuItem}', [MenuItemController::class, 'show'])->name('menuitems.show');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::delete('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::middleware('auth')->group(function () {
     Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
     Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
     Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
     Route::get('/my-orders', [OrderController::class, 'myOrders'])->name('orders.my-orders');
 });

//Route::get('/my-orders', [OrderController::class, 'myOrders'])->middleware('auth');
//Route::get('/orders/my-orders', [OrderController::class, 'myOrders'])->name('orders.my-orders');
require __DIR__.'/auth.php';
