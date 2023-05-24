<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureUserIsAdmin;
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

Route::post('/user/create', 'App\Http\Controllers\UserController@createUser');

Route::post('/role', 'App\Http\Controllers\RoleController@createRole')->middleware(EnsureUserIsAdmin::class);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
