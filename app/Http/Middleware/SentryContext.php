<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Sentry\State\Scope;

class SentryContext
{
    /**
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (config('logging.sentry_enabled') && app()->bound('sentry')) {
            $user = auth()->user();
            if ($user) {
                \Sentry\configureScope(function (Scope $scope) use ($user): void {
                    $scope->setUser([
                        'id' => $user->getAuthIdentifier(),
                        'type' => get_class($user),
                    ]);
                });
            }
        }

        return $next($request);
    }
}
