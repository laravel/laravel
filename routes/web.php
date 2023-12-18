<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ArticleCategoryController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('/articles')->group(function () {
    Route::get('/', [ArticleController::class, 'list'])->name('article.list');
    Route::match(['get', 'post'], '/create', [ArticleController::class, 'create'])->name('article.create');
    Route::get('/{slug}', [ArticleController::class, 'single'])->name('article.single');
    Route::match(['get', 'post'], '/articles/{id}/edit', [ArticleController::class, 'edit'])->name('article.edit');
    Route::post('/articles/{id}/delete', [ArticleController::class, 'delete'])->name('article.delete');
});
