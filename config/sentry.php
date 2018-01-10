<?php

return [
    'enabled' => env('SENTRY_ENABLED', false),

    'dsn' => env('SENTRY_DSN'),
    'dsn_public' => env('SENTRY_DSN_PUBLIC'),

    // capture release as git sha
    'release' => @trim(@file_get_contents('.release')),

    // Capture bindings on SQL queries
    'breadcrumbs.sql_bindings' => true,

    // Capture default user context
    'user_context' => true,

    'logging' => [
        'enabled' => env('SENTRY_LOGGING_ENABLED', false),
    ],
];
