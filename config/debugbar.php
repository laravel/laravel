<?php

return [

    /*
     |--------------------------------------------------------------------------
     | Debugbar Settings
     |--------------------------------------------------------------------------
     |
     | Debugbar is enabled by default, when debug is set to true in app.php.
     | You can override the value by setting enable to true or false instead of null.
     | 
     | You can provide an array of URI's that must be ignored (eg. 'api/*')
     |
     */

    'enabled' => env('DEBUGBAR_ENABLED', null),
    'except' => [
        //
    ],

    /*
     |--------------------------------------------------------------------------
     | Storage settings
     |--------------------------------------------------------------------------
     |
     | DebugBar stores data for session/ajax requests.
     | You can disable this, so the debugbar stores data in headers/session,
     | but this can cause problems with large data collectors.
     | By default, file storage (in the storage folder) is used. Redis and PDO
     | can also be used. For PDO, run the package migrations first.
     |
     */
    'storage' => [
        'enabled'    => true,
        'driver'     => 'file', // redis, file, pdo, custom
        'path'       => storage_path('debugbar'), // For file driver
        'connection' => null,   // Leave null for default connection (Redis/PDO)
        'provider'   => '' // Instance of StorageInterface for custom driver
    ],

    /*
     |--------------------------------------------------------------------------
     | Vendors
     |--------------------------------------------------------------------------
     |
     | Vendor files are included by default, but can be set to false.
     | This can also be set to 'js' or 'css', to only include javascript or css vendor files.
     | Vendor files are for css: font-awesome (including fonts) and highlight.js (css files)
     | and for js: jquery and and highlight.js
     | So if you want syntax highlighting, set it to true.
     | jQuery is set to not conflict with existing jQuery scripts.
     |
     */

    'include_vendors' => true,

    /*
     |--------------------------------------------------------------------------
     | Capture Ajax Requests
     |--------------------------------------------------------------------------
     |
     | The Debugbar can capture Ajax requests and display them. If you don't want this (ie. because of errors),
     | you can use this option to disable sending the data through the headers.
     |
     | Optionally, you can also send ServerTiming headers on ajax requests for the Chrome DevTools.
     */

    'capture_ajax' => true,
    'add_ajax_timing' => false,

    /*
     |--------------------------------------------------------------------------
     | Custom Error Handler for Deprecated warnings
     |--------------------------------------------------------------------------
     |
     | When enabled, the Debugbar shows deprecated warnings for Symfony components
     | in the Messages tab.
     |
     */
    'error_handler' => false,
    
    /*
     |--------------------------------------------------------------------------
     | Clockwork integration
     |--------------------------------------------------------------------------
     |
     | The Debugbar can emulate the Clockwork headers, so you can use the Chrome
     | Extension, without the server-side code. It uses Debugbar collectors instead.
     |
     */
    'clockwork' => false,

    /*
     |--------------------------------------------------------------------------
     | DataCollectors
     |--------------------------------------------------------------------------
     |
     | Enable/disable DataCollectors
     |
     */

    'collectors' => [
        'phpinfo'         => true,  // Php version
        'messages'        => true,  // Messages
        'time'            => true,  // Time Datalogger
        'memory'          => true,  // Memory usage
        'exceptions'      => true,  // Exception displayer
        'log'             => true,  // Logs from Monolog (merged in messages if enabled)
        'db'              => true,  // Show database (PDO) queries and bindings
        'views'           => true,  // Views with their data
        'route'           => true,  // Current route information
        'auth'            => true, // Display Laravel authentication status
        'gate'            => true, // Display Laravel Gate checks
        'session'         => true,  // Display session data
        'symfony_request' => true,  // Only one can be enabled..
        'mail'            => true,  // Catch mail messages
        'laravel'         => false, // Laravel version and environment
        'events'          => false, // All events fired
        'default_request' => false, // Regular or special Symfony request logger
        'logs'            => false, // Add the latest log messages
        'files'           => false, // Show the included files
        'config'          => false, // Display config settings
        'cache'           => false, // Display cache events
    ],

    /*
     |--------------------------------------------------------------------------
     | Extra options
     |--------------------------------------------------------------------------
     |
     | Configure some DataCollectors
     |
     */

    'options' => [
        'auth' => [
            'show_name' => true,   // Also show the users name/email in the debugbar
        ],
        'db' => [
            'with_params'       => true,   // Render SQL with the parameters substituted
            'backtrace'         => true,   // Use a backtrace to find the origin of the query in your files.
            'timeline'          => false,  // Add the queries to the timeline
            'explain' => [                 // Show EXPLAIN output on queries
                'enabled' => false,
                'types' => ['SELECT'],     // ['SELECT', 'INSERT', 'UPDATE', 'DELETE']; for MySQL 5.6.3+
            ],
            'hints'             => true,    // Show hints for common mistakes
        ],
        'mail' => [
            'full_log' => false
        ],
        'views' => [
            'data' => false,    //Note: Can slow down the application, because the data can be quite large..
        ],
        'route' => [
            'label' => true  // show complete route on bar
        ],
        'logs' => [
            'file' => null
        ],
        'cache' => [
            'values' => true // collect cache values
        ],
    ],

    /*
     |--------------------------------------------------------------------------
     | Inject Debugbar in Response
     |--------------------------------------------------------------------------
     |
     | Usually, the debugbar is added just before </body>, by listening to the
     | Response after the App is done. If you disable this, you have to add them
     | in your template yourself. See http://phpdebugbar.com/docs/rendering.html
     |
     */

    'inject' => true,

    /*
     |--------------------------------------------------------------------------
     | DebugBar route prefix
     |--------------------------------------------------------------------------
     |
     | Sometimes you want to set route prefix to be used by DebugBar to load
     | its resources from. Usually the need comes from misconfigured web server or
     | from trying to overcome bugs like this: http://trac.nginx.org/nginx/ticket/97
     |
     */
    'route_prefix' => '_debugbar',

    /*
     |--------------------------------------------------------------------------
     | DebugBar route domain
     |--------------------------------------------------------------------------
     |
     | By default DebugBar route served from the same domain that request served.
     | To override default domain, specify it as a non-empty value.
     */
    'route_domain' => null,
];
