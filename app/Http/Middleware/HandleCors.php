<?php

namespace App\Http\Middleware;

use Fruitcake\Cors\HandleCors as Middleware;

class HandleCors extends Middleware
{
    /**
     * The paths to enable CORS on.
     * Example: ['api/*']
     *
     * @var array
     */
    protected $paths = [];
}
