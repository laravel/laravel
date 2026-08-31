<?php

namespace Laravel\Sanctum\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateSession
{
    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth  The authentication factory implementation.
     */
    public function __construct(protected AuthFactory $auth)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->hasSession() || ! $request->user()) {
            return $next($request);
        }

        $guards = Collection::make(Arr::wrap(config('sanctum.guard')))
            ->mapWithKeys(fn ($guard) => [$guard => $this->auth->guard($guard)])
            ->filter(fn ($guard) => $guard instanceof SessionGuard);

        $shouldLogout = $guards->filter(
            fn ($guard, $driver) => $request->session()->has('password_hash_'.$driver)
        )->filter(
            fn ($guard, $driver) => ! $this->validatePasswordHash(
                $guard,
                $request->user()->getAuthPassword(),
                $request->session()->get('password_hash_'.$driver)
            )
        );

        if ($shouldLogout->isNotEmpty()) {
            $shouldLogout->each->logoutCurrentDevice();

            $request->session()->flush();

            throw new AuthenticationException('Unauthenticated.', [...$shouldLogout->keys()->all(), 'sanctum']);
        }

        return tap($next($request), function () use ($request, $guards) {
            if (! is_null($guard = $this->getFirstGuardWithUser($guards->keys()))) {
                $this->storePasswordHashInSession($request, $guard);
            }
        });
    }

    /**
     * Get the first authentication guard that has a user.
     *
     * @param  \Illuminate\Support\Collection  $guards
     * @return string|null
     */
    protected function getFirstGuardWithUser(Collection $guards)
    {
        return $guards->first(function ($guard) {
            $guardInstance = $this->auth->guard($guard);

            return method_exists($guardInstance, 'hasUser') &&
                   $guardInstance->hasUser();
        });
    }

    /**
     * Store the user's current password hash in the session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $guard
     * @return void
     */
    protected function storePasswordHashInSession($request, string $guard)
    {
        $guardInstance = $this->auth->guard($guard);

        $request->session()->put([
            "password_hash_{$guard}" => method_exists($guardInstance, 'hashPasswordForCookie')
                ? $guardInstance->hashPasswordForCookie($guardInstance->user()->getAuthPassword())
                : $guardInstance->user()->getAuthPassword(),
        ]);
    }

    /**
     * Validate the password hash against the stored value.
     *
     * @param  \Illuminate\Auth\SessionGuard  $guard
     * @param  string|null  $passwordHash
     * @param  string  $storedValue
     * @return bool
     */
    protected function validatePasswordHash(SessionGuard $guard, ?string $passwordHash, string $storedValue): bool
    {
        // Try new HMAC format first (Laravel 12.45.0+)...
        if (method_exists($guard, 'hashPasswordForCookie')) {
            if (hash_equals($guard->hashPasswordForCookie($passwordHash), $storedValue)) {
                return true;
            }
        }

        // Fall back to raw password hash format for backward compatibility...
        return hash_equals($passwordHash ?? '', $storedValue);
    }
}
