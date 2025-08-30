<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('gutotradebot')->group(function () {
    Route::get('/', 'GutoTradeBotController@index');
});

Route::prefix('payments')->group(function () {
    Route::post('/render/{id}', 'PaymentsController@renderById')->name('payments.render.id');
});

/*

    public function renderById($id)
    {
        $user = 1;
        $response = $bot->TelegramController->exportFileLocally("AgACAgEAAxkBAAIEtWgU3p93-ImyhVgfK2DpzEE3tJKkAALarjEbnW6oRFe8vIv8tEB3AQADAgADeQADNgQ", $bot->token);
        return view('2fa.enable', ['qrCodeUrl' => $user]);
    }
*/
