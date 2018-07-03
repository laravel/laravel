<?php

Route::get('/')
    ->use('Home\HomePageController')
    ->name('home-page.show');

Route::get('/login')
    ->use('Sessions\SessionController@create')
    ->name('session.create');

Route::get('/register')
    ->use('Accounts\AccountController@create')
    ->name('accounts.create');

Route::get('/awaiting-verification')
    ->use('Accounts/Verification/VerifyCodeController')
    ->name('verify-codes.create');

Route::get('/verify-codes/{verify-code}')
    ->use('Accounts/Verification/VerifyCodeController')
    ->name('verify-codes.show');

Route::get('/forgot-password')
    ->use('Accounts/PasswordResetController@create')
    ->name('password-resets.create');

Route::get('/password-resets/{token}')
    ->use('Accounts/PasswordResetController')
    ->name('password-resets.show');

Route::get('/account')
    ->use('Accounts/AccountDetailsPageController')
    ->name('account-details-page.show');
