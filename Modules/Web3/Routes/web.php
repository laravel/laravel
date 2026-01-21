<?php

use Modules\Web3\Http\Controllers\Web3Controller;
use Modules\Web3\Http\Controllers\WalletsController;

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

Route::prefix('web3')->group(function () {
    Route::get('/', [Web3Controller::class, 'index'])->name('web3.index');

    // Agrega más rutas según necesites
});

Route::prefix('wallets')->group(function () {
    Route::post('/isregistered/{address}', [WalletsController::class, 'isregistered'])->name('wallet.isregistered');
    Route::post('/register', [WalletsController::class, 'register'])->name('wallet.register');
});
