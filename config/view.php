<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most templating systems load templates from disk. Here you may specify
    | an array of paths that should be checked for your views. Of course
    | the usual Laravel view path has already been registered for you.
    |
    */

    'paths' => [
        realpath(base_path('resources/views')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | This option determines where all the compiled Blade templates will be
    | stored for your application. Typically, this is within the storage
    | directory. However, as usual, you are free to change this value.
    |
    */

    'compiled' => realpath(storage_path('framework/views')),

    /*
    |--------------------------------------------------------------------------
    | Should Views Be Cached
    |--------------------------------------------------------------------------
    |
    | Under some instances, such as when developing, you might want to disable
    | caching Blade templates in order to prevent unwanted caching. You may
    | change the following to false if you want views compiled each time.
    |
     */

    'should_cache' => true,

];
