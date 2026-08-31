<?php

namespace Laravel\Sanctum\Http\Middleware;

use Laravel\Sanctum\Exceptions\MissingScopeException;

/**
 * @deprecated
 * @see \Laravel\Sanctum\Http\Middleware\CheckAbilities
 */
class CheckScopes
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$scopes
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\AuthenticationException|\Laravel\Sanctum\Exceptions\MissingScopeException
     */
    public function handle($request, $next, ...$scopes)
    {
        try {
            return (new CheckAbilities())->handle($request, $next, ...$scopes);
        } catch (\Laravel\Sanctum\Exceptions\MissingAbilityException $e) {
            throw new MissingScopeException($e->abilities());
        }
    }
}
