<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode as Middleware;
use Illuminate\Http\Request;

class CheckForMaintenanceMode extends Middleware
{
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
        return in_array(
            $this->clientIp($request),
            config('app.maintenance_mode.whitelist'),
            false
        );
    }

    /**
     * Requester IP Address.
     */
    private function clientIp(Request $request): string
    {
        $forwarded = $request->server('HTTP_X_FORWARDED_FOR');
        if ($forwarded) {
            $forwarded = explode(',', $forwarded);

            return trim($forwarded[0]);
        }

        return $request->server('REMOTE_ADDR');
    }
}
