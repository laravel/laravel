<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MercadoLivreController;

Route::get('/auth', [MercadoLivreController::class, 'redirectToML']);
Route::get('/callback', [MercadoLivreController::class, 'handleCallback']);
Route::get('/produto/create', [ProdutoController::class, 'create']);
Route::post('/produto/store', [ProdutoController::class, 'store']);


class Produto extends Model
{
    use HasFactory;
}
