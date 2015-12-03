<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Drivers
    |--------------------------------------------------------------------------
    |
    | Here you may define every authentication driver for your application.
    | Of course, a default and working configuration is already defined
    | here but you are free to define additional drivers when needed.
    |
    | The "guard" option defines the default driver that will be used when
    | utilizing the "Auth" facade within your application. But, you may
    | access every other auth driver via the facade's "guard" method.
    |
    | All authentication drivers have a "provider". A provider defines how
    | users are actually retrieved out of the database or other storage
    | mechanism used by your application to persist your user's data.
    |
    | Supported: "session"
    |
    */

    'guard' => 'session',

    'guards' => [
        'session' => [
            'driver' => 'session',
            'provider' => 'eloquent',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication drivers have a "provider". A provider defines how
    | users are actually retrieved out of the database or other storage
    | mechanism used by your application to persist your user's data.
    |
    | Supported: "database", "eloquent"
    |
    */

    'providers' => [
        'eloquent' => [
            'driver' => 'eloquent',
            'model' => App\User::class,
        ],

        'database' => [
            'driver' => 'database',
            'table' => 'users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Reset Settings
    |--------------------------------------------------------------------------
    |
    | Here you may set the options for resetting passwords including the view
    | that is your password reset e-mail. You can also set the name of the
    | table that maintains all of the reset tokens for your application.
    |
    | Of course, you may define multiple password "brokers" each with a their
    | own storage settings and user providers. However, for most apps this
    | default configuration of using Eloquent is perfect out of the box.
    |
    | The expire time is the number of minutes that the reset token should be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    */

    'broker' => 'default',

    'brokers' => [
        'default' => [
            'provider' => 'eloquent',
            'email' => 'emails.password',
            'table' => 'password_resets',
            'expire' => 60,
        ],
    ],

];
