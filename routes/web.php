<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\MercadoLivreController;

// Rota para a página inicial que redireciona para a criação de produtos
Route::get('/', [ProdutoController::class, 'create'])->name('produtos.create');

// Rotas para produtos
Route::get('/produto/create', [ProdutoController::class, 'create'])->name('produtos.create');
Route::post('/produto/store', [ProdutoController::class, 'store'])->name('produtos.store');

// Rotas para integração com o Mercado Livre
Route::get('/mercadolivre/redirect', [MercadoLivreController::class, 'redirectToML'])->name('mercadolivre.redirect');
Route::get('/mercadolivre/callback', [MercadoLivreController::class, 'handleCallback'])->name('mercadolivre.callback');
