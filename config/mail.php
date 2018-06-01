<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mail Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the mail connections below you wish
    | to use as your default connection for all mail work. Of course
    | you may use many connections at once using the Mail library.
    |
    */

    'default' => env('MAIL_CONNECTION', 'smtp'),

    /*
    |--------------------------------------------------------------------------
    | Mail Connections
    |--------------------------------------------------------------------------
    |
    | This section contains the mail connections setup for your application.
    | Laravel supports both SMTP and PHP's "mail" function as drivers for the
    | sending of e-mail. You may specify which one you're using throughout
    | your application here. By default, Laravel is setup for SMTP mail.
    |
    | Supported: "smtp", "sendmail", "mailgun", "mandrill", "ses",
    |            "sparkpost", "log", "array"
    |
    */

    'connections' => [

        'smtp' => [
            'driver' => 'smtp',
            'host' => env('MAIL_HOST'),
            'port' => env('MAIL_PORT', 587),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
        ],

        'sendmail' => [
            'driver' => 'sendmail',
            'path' => env('MAIL_SENDMAIL', '/usr/sbin/sendmail -bs'),
        ],

        'mailgun' => [
            'driver' => 'mailgun',
            'host' => env('MAIL_HOST', 'smtp.mailgun.org'),
            'service' => env('MAIL_MAILGUN_SERVICE', 'mailgun'),
        ],

        'mandrill' => [
            'driver' => 'mandrill',
        ],

        'ses' => [
            'driver' => 'ses',
            'service' => env('MAIL_SES_SERVICE', 'ses'),
        ],

        'sparkpost' => [
            'driver' => 'sparkpost',
            'service' => env('MAIL_SPARKPOST_SERVICE', 'sparkpost'),
        ],

        'log' => [
            'driver' => 'log',
        ],

        'array' => [
            'driver' => 'array',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    |
    | You may wish for all e-mails sent by your application to be sent from
    | the same address. Here, you may specify a name and address that is
    | used globally for all e-mails that are sent by your application.
    |
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Example'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Markdown Mail Settings
    |--------------------------------------------------------------------------
    |
    | If you are using Markdown based email rendering, you may configure your
    | theme and component paths here, allowing you to customize the design
    | of the emails. Or, you may simply stick with the Laravel defaults!
    |
    */

    'markdown' => [
        'theme' => 'default',

        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],

];
