<?php

return [

    'name' => env('API_NAME', env('APP_NAME', 'Larapi')),

    'version' => env('API_VERSION', 'v1'),

    'prefix' => env('API_PREFIX', 'api'),

    'rate_limit' => env('API_RATE_LIMIT', '60,1'),

];
