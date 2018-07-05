<?php

/*
|--------------------------------------------------------------------------
| Login
|--------------------------------------------------------------------------
*/

Route::post('session')
    ->uses('Accounts\SessionController@store')
    ->name('session.create');

Route::delete('session')
    ->uses('Accounts\SessionController@destroy')
    ->name('session.destroy');

/*
|--------------------------------------------------------------------------
| Accounts
|--------------------------------------------------------------------------
*/

Route::resource('accounts', 'Accounts\AccountController')
    ->only('store', 'update');

/*
|--------------------------------------------------------------------------
| Account Verification
|--------------------------------------------------------------------------
*/

Route::resource('verify-codes', 'Accounts\Verification\VerifyCodeController')
    ->only('store');

/*
|--------------------------------------------------------------------------
| Password Resets
|--------------------------------------------------------------------------
*/

Route::resource('password-resets', 'Accounts\PasswordResetController')
    ->parameter('passwordReset', 'token')
    ->only('store', 'destroy');
