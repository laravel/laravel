<?php

Route::post('session')
    ->use('Sessions\SessionController@store')
    ->name('session.create');

Route::delete('session')
    ->use('Sessions\SessionController@destroy')
    ->name('session.destroy');

Route::resource('accounts', 'Accounts\AccountController')
    ->only('store');

Route::resource('verify-codes', 'Accounts\Verification\VerifyCodeController')
    ->only('store', 'delete');

Route::resource('password-resets', 'Accounts\PasswordResetController')
    ->parameter('token')
    ->only('store', 'destroy');
