<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/', [ProductController::class, 'selling'])->name('selling');
Route::post('/sell', [ProductController::class, 'processSale'])->name('sell.process');

Route::get('/manage', [ProductController::class, 'manage'])->name('manage');
Route::post('/manage', [ProductController::class, 'store'])->name('products.store');
Route::put('/manage/update-stock', [ProductController::class, 'updateStock'])->name('products.updateStock');
Route::delete('/manage/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

Route::get('/store', [ProductController::class, 'viewStore'])->name('store');
