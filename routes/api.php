<?php

use Illuminate\Http\Request;
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
// in a routes file

//Route::post('/submit-form', function () {
//    //
//})->middleware(\Spatie\HttpLogger\Middlewares\HttpLogger::class);


Route::group(['prefix'=>'v1'], function () {
    Route::get('status','App\Http\Controllers\Controller@status');
});

