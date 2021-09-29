<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\IpUtils;

class PreventRequestsDuringMaintenance extends Middleware
{
    /**
     * The URIs that should be reachable while maintenance mode is enabled.
     *
     * @var array
     */
    protected $except = [
    ];

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->app->isDownForMaintenance()) {
            if (! $this->isWhitelisted($request)) {
                parent::handle($request, $next);
            }
        }

        return $next($request);
    }

    /**
     * Check if the ips are in the whitelist.
     */
    private function isWhitelisted(Request $request): bool
    {
        return IpUtils::checkIp($request->getClientIp(), config('app.maintenance_mode.whitelist'));
    }
}
