<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enabled
    |--------------------------------------------------------------------------
    |
    | Turns the frontend routes on. Should be disabled on production.
    |
    */
    'enabled' => env('TEMPLATES_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Route Name
    |--------------------------------------------------------------------------
    |
    | Name prefix of the routes defined by the package
    | e.g. route('templates.index')
    |
    */
    'route_name' => 'templates',

    /*
    |--------------------------------------------------------------------------
    | Route Path
    |--------------------------------------------------------------------------
    |
    | URL path prefix of the  routes defined by the package
    | e.g. /templates/
    |
    */
    'route_path' => 'templates',

    /*
    |--------------------------------------------------------------------------
    | Resource Path
    |--------------------------------------------------------------------------
    |
    | Path prefix within the /resources/ folder that the package looks for
    | the frontend templates.
    |
    */
    'resource_path' => 'templates',

    /*
    |--------------------------------------------------------------------------
    | Route Middleware
    |--------------------------------------------------------------------------
    |
    | Middlewares to run when adding frontend routes.
    |
    */
	'middleware' => [ 'web' ],

	/*
    |--------------------------------------------------------------------------
    | Frontend Template Flag
    |--------------------------------------------------------------------------
    |
    */
    'template_flag' => 'template',

    /*
    |--------------------------------------------------------------------------
    | Show Styleguide Link
    |--------------------------------------------------------------------------
    |
    | If enabled, an external link to the styleguide will be displayed.
    |
    */
    'show_styleguide' => true,

];
