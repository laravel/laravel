<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Registration Open
    |--------------------------------------------------------------------------
    |
    | Choose whether new users/admins are allowed to register.
    | This will show the Register button on the login page and allow access to the
    | Register functions in AuthController.
    |
    | By default the registration is open only on localhost.
    */

    'registration_open' => env('BACKPACK_REGISTRATION_OPEN', env('APP_ENV') === 'local'),

    /*
    |--------------------------------------------------------------------------
    | Routing
    |--------------------------------------------------------------------------
    */

    // The prefix used in all base routes (the 'admin' in admin/dashboard)
    // You can make sure all your URLs use this prefix by using the backpack_url() helper instead of url()
    'route_prefix' => 'admin',

    // The web middleware (group) used in all base & CRUD routes
    // If you've modified your "web" middleware group (ex: removed sessions), you can use a different
    // route group, that has all the the middleware listed below in the comments.
    'web_middleware' => 'web',
    // Or you can comment the above, and uncomment the complete list below.
    // 'web_middleware' => [
    //     \App\Http\Middleware\EncryptCookies::class,
    //     \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    //     \Illuminate\Session\Middleware\StartSession::class,
    //     \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    //     \App\Http\Middleware\VerifyCsrfToken::class,
    // ],

    // Set this to false if you would like to use your own AuthController and PasswordController
    // (you then need to setup your auth routes manually in your routes.php file)
    // Warning: if you disable this, the password recovery routes (below) will be disabled too!
    'setup_auth_routes' => true,

    // Set this to false if you would like to skip adding the dashboard routes
    // (you then need to overwrite the login route on your AuthController)
    'setup_dashboard_routes' => true,

    // Set this to false if you would like to skip adding "my account" routes
    // (you then need to manually define the routes in your web.php)
    'setup_my_account_routes' => true,

    // Set this to false if you would like to skip adding the password recovery routes
    // (you then need to manually define the routes in your web.php)
    'setup_password_recovery_routes' => true,

    // Set this to true if you would like to enable email verification for your user model.
    // Make sure your user model implements the MustVerifyEmail contract and your database
    // table contains the `email_verified_at` column. Read the following before enabling:
    // https://backpackforlaravel.com/docs/6.x/base-how-to#enable-email-verification-in-backpack-routes
    'setup_email_verification_routes' => false,

    // When email verification is enabled, automatically add the Verified middleware to Backpack routes?
    // Set false if you want to use your own Verified middleware in `middleware_class`.
    'setup_email_verification_middleware' => true,

    // How many times in any given time period should the user be allowed to
    // request a new verification email?
    // Defaults to 1,10 - 1 time in 10 minutes.
    'email_verification_throttle_access' => '3,15',

    /*
    |--------------------------------------------------------------------------
    | Security
    |--------------------------------------------------------------------------
    */

    // Backpack will prevent visitors from requesting password recovery too many times
    // for a certain email, to make sure they cannot be spammed that way.
    // How many seconds should a visitor wait, after they've requested a
    // password reset, before they can try again for the same email?
    'password_recovery_throttle_notifications' => 600, // time in seconds

    // How much time should the token sent to the user email be considered valid?
    // After this time expires, user needs to request a new reset token.
    'password_recovery_token_expiration' => 60, // time in minutes

    // Backpack will prevent an IP from trying to reset the password too many times,
    // so that a malicious actor cannot try too many emails, too see if they have
    // accounts or to increase the AWS/SendGrid/etc bill.
    //
    // How many times in any given time period should the user be allowed to
    // attempt a password reset? Take into account that user might wrongly
    // type an email at first, so at least allow one more try.
    // Defaults to 3,10 - 3 times in 10 minutes.
    'password_recovery_throttle_access' => '3,10',

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

    // Fully qualified namespace of the User model
    'user_model_fqn' => config('auth.providers.users.model'),
    // 'user_model_fqn' => App\User::class, // works on Laravel <= 7
    // 'user_model_fqn' => App\Models\User::class, // works on Laravel >= 8

    // The classes for the middleware to check if the visitor is an admin
    // Can be a single class or an array of classes
    'middleware_class' => [
        App\Http\Middleware\CheckIfAdmin::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \Backpack\CRUD\app\Http\Middleware\AuthenticateSession::class,
        // \Backpack\CRUD\app\Http\Middleware\UseBackpackAuthGuardInsteadOfDefaultAuthGuard::class,
    ],

    // Alias for that middleware
    'middleware_key' => 'admin',
    // Note: It's recommended to use the backpack_middleware() helper everywhere, which pulls this key for you.

    // Username column for authentication
    // The Backpack default is the same as the Laravel default (email)
    // If you need to switch to username, you also need to create that column in your db
    'authentication_column' => 'email',
    'authentication_column_name' => 'Email',

    // Backpack assumes that your "database email column" for operations like Login and Register is called "email".
    // If your database email column have a different name, you can configure it here. Eg: `user_mail`
    'email_column' => 'email',

    // The guard that protects the Backpack admin panel.
    // If null, the config.auth.defaults.guard value will be used.
    'guard' => 'backpack',

    // The password reset configuration for Backpack.
    // If null, the config.auth.defaults.passwords value will be used.
    'passwords' => 'backpack',

    // What kind of avatar will you like to show to the user?
    // Default: gravatar (automatically use the gravatar for their email)
    // Other options:
    // - null (generic image with their first letter)
    // - example_method_name (specify the method on the User model that returns the URL)
    'avatar_type' => 'gravatar',

    // Gravatar fallback options are 'identicon', 'monsterid', 'wavatar', 'retro', 'robohash', 'blank'
    // 'blank' will keep the generic image with the user first letter
    'gravatar_fallback' => 'blank',

    /*
    |--------------------------------------------------------------------------
    | File System
    |--------------------------------------------------------------------------
    */

    // Backpack\Base sets up its own filesystem disk, just like you would by
    // adding an entry to your config/filesystems.php. It points to the root
    // of your project and it's used throughout all Backpack packages.
    //
    // You can rename this disk here. Default: root
    'root_disk_name' => 'root',

    /*
    |--------------------------------------------------------------------------
    | Application
    |--------------------------------------------------------------------------
    */

    // Should we use DB transactions when executing multiple queries? For example when creating an entry and it's relationships.
    // By wrapping in a database transaction you ensure that either all queries went ok, or if some failed the whole process
    // is rolled back and considered failed. This is a good setting for data integrity.
    'useDatabaseTransactions' => false,

    /*
    |--------------------------------------------------------------------------
    | Backpack Token Username
    |--------------------------------------------------------------------------
    |
    | If you have access to closed-source Backpack add-ons, please provide
    | your token username here, if you're getting yellow alerts on your
    | admin panel's pages. Normally this is not needed, it is
    | preferred to add this as an environment variable
    | (most likely in your .env file).
    |
    | More info and payment form on:
    | https://www.backpackforlaravel.com
    |
    */

    'token_username' => env('BACKPACK_TOKEN_USERNAME', false),
];
