<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    |
    | This option controls the default mailer that is used to send any email
    | messages sent by your application. Alternative mailers may be setup
    | and used as needed; however, this mailer will be used by default.
    |
    */

    'default' => env('MAIL_MAILER', 'smtp'),

    'mailers' => [

        'smtp' => [

            /*
            |--------------------------------------------------------------------------
            | Mail Transport Driver
            |--------------------------------------------------------------------------
            |
            | Laravel supports a variety of mail "transport" drivers to be used while
            | sending an e-mail. You will specify which one you are using for this
            | mailer here. The mailer is configured to send via SMTP by default.
            |
            | Supported: "smtp", "sendmail", "mailgun", "ses",
            |            "postmark", "log", "array"
            |
            */

            'transport' => env('MAIL_TRANSPORT', 'smtp'),

            /*
            |--------------------------------------------------------------------------
            | SMTP Host Address
            |--------------------------------------------------------------------------
            |
            | Here you may provide the host address of the SMTP server used by your
            | applications. A default option is provided that is compatible with
            | the Mailgun mail service which will provide reliable deliveries.
            |
            */

            'host' => env('MAIL_HOST', 'smtp.mailgun.org'),

            /*
            |--------------------------------------------------------------------------
            | SMTP Host Port
            |--------------------------------------------------------------------------
            |
            | This is the SMTP port used by this mailer when delivering e-mails to
            | users of the application. Like the host we have set this value to
            | stay compatible with the Mailgun e-mail application by default.
            |
            */

            'port' => env('MAIL_PORT', 587),

            /*
            |--------------------------------------------------------------------------
            | Global "From" Address
            |--------------------------------------------------------------------------
            |
            | You may wish for all e-mails sent by your application to be sent from
            | the same address. Here, you may specify a name and address that is
            | used globally for all e-mails that are sent through this mailer.
            |
            */

            'from' => [
                'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
                'name' => env('MAIL_FROM_NAME', 'Example'),
            ],

            /*
            |--------------------------------------------------------------------------
            | E-Mail Encryption Protocol
            |--------------------------------------------------------------------------
            |
            | Here you may specify the encryption protocol that should be used when
            | the mailer sends any e-mail messages. A sensible default using the
            | transport layer security protocol should provide great security.
            |
            */

            'encryption' => env('MAIL_ENCRYPTION', 'tls'),

            /*
            |--------------------------------------------------------------------------
            | SMTP Server Username
            |--------------------------------------------------------------------------
            |
            | If your SMTP server requires a username for authentication, you should
            | set it here. This will get used to authenticate with your server on
            | connection. You may also set the "password" value below this one.
            |
            */

            'username' => env('MAIL_USERNAME'),

            'password' => env('MAIL_PASSWORD'),

        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Sendmail System Path
    |--------------------------------------------------------------------------
    |
    | When using the "sendmail" driver to send e-mails, we will need to know
    | the path to where Sendmail lives on this server. A default path has
    | been provided here, which will work well on most of your systems.
    |
    */

    'sendmail' => '/usr/sbin/sendmail -bs',

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

    /*
    |--------------------------------------------------------------------------
    | Log Channel
    |--------------------------------------------------------------------------
    |
    | If you are using the "log" driver, you may specify the logging channel
    | if you prefer to keep mail messages separate from other log entries
    | for simpler reading. Otherwise, the default channel will be used.
    |
    */

    'log_channel' => env('MAIL_LOG_CHANNEL'),

];
