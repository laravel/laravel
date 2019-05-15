<?php

return [
    'enabled' => env('SENTRY_ENABLED', false),

    'dsn' => env('SENTRY_DSN'),

    // capture release as git sha
    //'release' => @trim(@file_get_contents('.release')),

    // Capture default user context
    'user_context' => true,

    'logging' => [
        'enabled' => env('SENTRY_LOGGING_ENABLED', false),
    ],

    'breadcrumbs' => [
        // Capture bindings on SQL queries logged in breadcrumbs
        'sql_bindings' => true,
    ],
];
