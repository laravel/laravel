<?php

namespace Illuminate\Routing;

class RouteFileRegistrar
{
    /**
     * The router instance.
     *
     * @var \Illuminate\Routing\Router
     */
    protected $router;

    /**
     * Create a new route file registrar instance.
     *
     * @param  \Illuminate\Routing\Router  $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Require the given routes file.
     *
     * @param  string  $routes
     * @return void
     */
    public function register($routes)
    {
        $router = $this->router;

        require $routes;
    }
}
