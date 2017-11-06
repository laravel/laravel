<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Password hashing Algorithm
    |--------------------------------------------------------------------------
    |
    | Laravel supports both Bcrypt and Argon2i Hashing algorithm as drivers for the
    | Protection of passwords. You may specify which one you're using throughout
    | your application here.
    |
    | By default, Laravel is setup for the Bcrypt algorithm.
    |
    */

    'driver' => env('HASHING_DRIVER', 'bcrypt'),
];
