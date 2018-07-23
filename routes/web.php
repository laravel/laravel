<?php

/*
|------------------------------------------------------------------------------
| Pages
|------------------------------------------------------------------------------
*/

Route::get('/')
    ->uses('Home\HomeController')
    ->name('home.show');

/*
|------------------------------------------------------------------------------
| Login
|------------------------------------------------------------------------------
*/

Route::get('/login')
    ->uses('Accounts\SessionController@create')
    ->name('session.create');

/*
|------------------------------------------------------------------------------
| Accounts
|------------------------------------------------------------------------------
*/

Route::get('/register')
    ->uses('Accounts\AccountController@create')
    ->name('accounts.create');

/*
|------------------------------------------------------------------------------
| Account Verification
|------------------------------------------------------------------------------
*/

Route::get('/account/almost-there')
    ->uses('Accounts\Verification\VerifyCodeController@create')
    ->name('verify-codes.create');

Route::resource('/verify-codes', 'Accounts\Verification\VerifyCodeController')
    ->only('show');

/*
|------------------------------------------------------------------------------
| Password Resets
|------------------------------------------------------------------------------
*/

Route::get('/forgot-password')
    ->uses('Accounts\PasswordResetController@create')
    ->name('password-resets.create');

Route::get('/password-resets/{token}')
    ->uses('Accounts\PasswordResetController@show')
    ->name('password-resets.show');
