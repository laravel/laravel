<?php

use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Laravel\Sanctum\Http\Middleware\AuthenticateSession;

return [

    /*
    |--------------------------------------------------------------------------
    | Stateful Domains
    |--------------------------------------------------------------------------
    |
    | Larapi uses bearer token authentication only. Leave this empty unless
    | you intentionally add SPA cookie auth later.
    |
    */

    'stateful' => array_filter(explode(',', env('SANCTUM_STATEFUL_DOMAINS', ''))),

    /*
    |--------------------------------------------------------------------------
    | Sanctum Guards
    |--------------------------------------------------------------------------
    |
    | Empty guard list keeps Sanctum on bearer tokens only.
    |
    */

    'guard' => [],

    'expiration' => env('SANCTUM_TOKEN_EXPIRATION'),

    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),

    'middleware' => [
        'authenticate_session' => AuthenticateSession::class,
        'encrypt_cookies' => EncryptCookies::class,
        'validate_csrf_token' => ValidateCsrfToken::class,
    ],

];
