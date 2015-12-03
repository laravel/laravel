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
    | The "default_guard" option is the default driver which is used while
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

    'default_guard' => 'web',

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'eloquent',
        ],

        // 'api' => [

        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication drivers have a "provider". A provider defines how
    | users are actually retrieved out of the database or other storage
    | mechanisms used by the application to persist your user's data.
    |
    | Supported: "database", "eloquent"
    |
    */

    'providers' => [
        'eloquent' => [
            'driver' => 'eloquent',
            'model' => App\User::class,
        ],

        // 'database' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Resets
    |--------------------------------------------------------------------------
    |
    | Here you may set the options for resetting passwords including the view
    | that is your password reset e-mail. You can also set the name of the
    | table that maintains all of the reset tokens for your application.
    |
    | Of course, you may define multiple password resetters each with a their
    | own storage settings and user providers. However, for most apps this
    | default configuration of using Eloquent is perfect out of the box.
    |
    | The expire time is the number of minutes that the reset token should be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    */

    'default_resetter' => 'default',

    'resetters' => [
        'default' => [
            'provider' => 'eloquent',
            'email' => 'emails.password',
            'table' => 'password_resets',
            'expire' => 60,
        ],
    ],

];
