<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enabled
    |--------------------------------------------------------------------------
    |
    | Turns the frontend routes on. You should ensure this is set to false in
    | production environments.
    |
    */
    'enabled' => env('FRONTEND_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Route Name
    |--------------------------------------------------------------------------
    |
    | Name prefix of the routes defined by the package, e.g.:
    |
    | route('frontend.index')
    |
    */
    'route_name' => 'frontend',

    /*
    |--------------------------------------------------------------------------
    | Route Path
    |--------------------------------------------------------------------------
    |
    | URL path prefix of the  routes defined by the package, e.g.:
    |
    | /frontend/
    |
    */
    'route_path' => 'frontend',

    /*
    |--------------------------------------------------------------------------
    | Resource Path
    |--------------------------------------------------------------------------
    |
    | Path prefix within the resources/views/ folder that the package looks for
    | the frontend templates.
    |
    */
    'resource_path' => 'frontend',

    /*
    |--------------------------------------------------------------------------
    | Index Template Path
    |--------------------------------------------------------------------------
    |
    | Path to the index template that lists out all the others, from the
    | resources/views/ folder.
    |
    */
    'index_template_path' => 'app/frontend',

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
    'template_flag' => 'frontend',

];
