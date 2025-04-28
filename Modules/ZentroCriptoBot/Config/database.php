<?php

return [
    'connection' => [
        'driver' => 'mysql',
        'url' => env('DATABASE_URL'),
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'database' => env('DB_DATABASE_ZENTRO_CRIPTO_BOT', env('DB_DATABASE')),
        'username' => env('DB_USERNAME_ZENTRO_CRIPTO_BOT', env('DB_USERNAME')),
        'password' => env('DB_PASSWORD_ZENTRO_CRIPTO_BOT', env('DB_PASSWORD')),
        'unix_socket' => env('DB_SOCKET', ''),
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => true,
        'engine' => null,
        'options' => extension_loaded('pdo_mysql') ? array_filter([
            PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
        ]) : [],
    ],
];
