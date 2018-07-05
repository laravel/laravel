<?php

/*
|--------------------------------------------------------------------------
| Pages
|--------------------------------------------------------------------------
*/

Route::get('/')
    ->uses('Home\HomePageController')
    ->name('page/home');

/*
|--------------------------------------------------------------------------
| Login
|--------------------------------------------------------------------------
*/

Route::get('/login')
    ->uses('Accounts\SessionController@create')
    ->name('session.create');

/*
|--------------------------------------------------------------------------
| Accounts
|--------------------------------------------------------------------------
*/

Route::get('/register')
    ->uses('Accounts\AccountController@create')
    ->name('accounts.create');

Route::get('/account')
    ->uses('Accounts\AccountController@store')
    ->name('page/account');

/*
|--------------------------------------------------------------------------
| Account Verification
|--------------------------------------------------------------------------
*/

Route::resource('/verify-codes', 'Accounts\Verification\VerifyCodeController')
    ->only('create', 'show');

/*
|--------------------------------------------------------------------------
| Password Resets
|--------------------------------------------------------------------------
*/

// Route::get('/forgot-password')
//     ->uses('Accounts/PasswordResetController@create')
//     ->name('password-resets.create');

// Route::get('/password-resets/{token}')
//     ->uses('Accounts/PasswordResetController')
//     ->name('password-resets.show');
