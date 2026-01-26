<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/telegrambot', function (Request $request) {
    return $request->user();
});

Route::prefix('telegram')->group(function () {
    // Ruta para el WebApp de escaneo
    // URL final ejemplo: https://tudominio.com/telegram/scanner
    Route::get('/scanner/{botname}/{instance?}', 'TelegramBotController@initScanner')->name('telegram-scanner-init');
    Route::get('/scanner/store', 'TelegramBotController@storeScan')->name('telegram-scanner-store');

});