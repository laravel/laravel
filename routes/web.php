<?php

use App\Http\Controllers\ProdcutController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
   });


   Route::get('/product', [ProdcutController::class, 'index'])->name('product.index');
   Route::get('/product/create', [ProdcutController::class, 'create'])->name('product.create');
   Route::post('/product', [ProdcutController::class, 'store'])->name('product.store');

