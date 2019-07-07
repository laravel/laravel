<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Encrypter
    |--------------------------------------------------------------------------
    |
    | The default encrypter will be used to encrypt cookies and sessions. You
    | can use the default encrypter for all of your encryption need or use
    | additional encrypters defined below.
    |
    */

    'default' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Encrypters
    |--------------------------------------------------------------------------
    |
    | Encrypters allow you to specify multiple encryption key and ciphers
    | to discretely encrypt data. Each key contained in the encrypters should
    | be set to a random, 32 or 16 character string, otherwise the encrypted
    | data will not be safe. Please do this before deploying an application!
    |
    */

    'encrypters' => [

        'default' => [
            'key' => env('APP_KEY'),
            'cipher' => 'AES-256-CBC',
        ],

    ]
];