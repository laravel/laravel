<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel'              => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | ArcanePay Services
    |--------------------------------------------------------------------------
    */

    // WhatsApp via Fonnte — https://fonnte.com
    'fonnte' => [
        'token'  => env('FONNTE_TOKEN'),
        'secret' => env('FONNTE_SECRET'),
    ],

    // Payment Gateway — Tripay — https://tripay.co.id
    'tripay' => [
        'api_key'       => env('TRIPAY_API_KEY'),
        'private_key'   => env('TRIPAY_PRIVATE_KEY'),
        'merchant_code' => env('TRIPAY_MERCHANT_CODE'),
        'env'           => env('TRIPAY_ENV', 'sandbox'),
    ],

    // Supplier #1 — Digiflazz — https://digiflazz.com
    'digiflazz' => [
        'username'     => env('DIGIFLAZZ_USERNAME'),
        'api_key_dev'  => env('DIGIFLAZZ_API_KEY_DEV'),
        'api_key_prod' => env('DIGIFLAZZ_API_KEY_PROD'),
        'env'          => env('DIGIFLAZZ_ENV', 'dev'),
    ],

    // Supplier #2 — VIP Reseller — https://vip-reseller.co.id
    'vipreseller' => [
        'api_key' => env('VIPRESELLER_API_KEY'),
        'api_id'  => env('VIPRESELLER_API_ID'),
        'active'  => env('VIPRESELLER_ACTIVE', false),
    ],

];
