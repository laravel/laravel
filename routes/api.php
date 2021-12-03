<?php

use App\Http\Controllers\UserAPIController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/users', [UserAPIController::class, 'getAllUsers']);
Route::post('/users/create', [UserAPIController::class, 'createUser']);
Route::post('/users/{id}', [UserAPIController::class, 'updateUser']);
