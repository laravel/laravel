<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Hash Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default hash driver that will be used to hash
    | passwords for your application. By default, the bcrypt algorithm is
    | used; however, you remain free to modify this option if you wish.
    |
    | Supported: "bcrypt", "argon"
    |
    */

    'driver' => 'bcrypt',

    /*
    |--------------------------------------------------------------------------
    | bcrypt options
    |--------------------------------------------------------------------------
    |
    | We could define the number of rounds the bcrypt algo will be using.
    |
    | The two digit cost parameter is the base-2 logarithm of the iteration
    | count for the underlying Blowfish-based hashing algorithmeter and must
    | be in range 04-31, values outside this range will cause crypt() to fail
    |
    | Default: 10
    */
    'bcrypt' => [
        'rounds' => 10
    ],

    /*
    |--------------------------------------------------------------------------
    | argon options
    |--------------------------------------------------------------------------
    |
    | These settings could be adjusted depending on your hardware.
    |
    | time: Maximum amount of time it may take to compute the Argon2 hash.
    |        (default: 2)
    |
    | memory: Maximum memory (in bytes) that may be used to compute the Argon2 hash
    |        (default : 1024)
    |
    | threads: Number of threads to use for computing the Argon2 hash
    |        (default : 2)
    |
    */
    'argon' => [
        'time' => 2,
        'memory' => 1024,
        'threads' => 2
    ]
];
