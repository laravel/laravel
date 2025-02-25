<?php

use App\Http\Controllers\ProdcutController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
   });


   Route::get('/product', [ProdcutController::class, 'index'])->name('products.index');

