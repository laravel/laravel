<?php

use App\Http\Controllers\Api\V1\BookController;
use Illuminate\Support\Facades\Route;

Route::get('nyt/best-sellers/',[
    BookController::class, 'bestSellers'
])->name('nyt.bestsellers');
