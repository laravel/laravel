<?php

return [
    'dsn' => env('SENTRY_DSN'),

    'breadcrumbs' => [
        // Capture bindings on SQL queries logged in breadcrumbs
        'sql_bindings' => true,
    ],
];
