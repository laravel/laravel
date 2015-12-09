<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode as Original;

class CheckForMaintenanceMode extends Original
{
    /**
     * Application routes not effected by the down command.
     *
     * @var array
     */
    protected $excludedRoutes = [];

    /**
     * IP Addresses not effected by the down command.
     *
     * @var array
     */
    protected $excludedIPs = [];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function handle($request, Closure $next)
    {

        if ($this->app->isDownForMaintenance()) {

            $response = $next($request);

            if (in_array($request->ip(), $this->excludedIPs)) {
                return $response;
            }

            $route = $request->route();

            if ($route instanceof Route) {

                if (in_array($route->getName(), $this->excludedRoutes)) {
                    return $response;
                }

            }

            // Add your own logic here to allow specific routes/users/requests
            // to bypass the down command by simply returning the response.

            throw new HttpException(503);
        }

        return $next($request);
    }
}
