<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable Multi Server Scheduling
    |--------------------------------------------------------------------------
    |
    | This option controls how the Laravel framework handles scheduling cron
    | events in a load balanced environment. Typically this option should
    | be enabled when you need multiple load balanced servers together.
    |
    */

    'multi_server' => env('MULTI_SERVER_SCHEDULING', true),
];
