<?php

namespace Illuminate\Foundation\Http\Middleware;

use Closure;
use ErrorException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Http\MaintenanceModeBypassCookie;
use Illuminate\Foundation\Http\Middleware\Concerns\ExcludesPaths;
use Illuminate\Support\Arr;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PreventRequestsDuringMaintenance
{
    use ExcludesPaths;

    /**
     * The application implementation.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The URIs that should be excluded.
     *
     * @var array<int, string>
     */
    protected $except = [];

    /**
     * The URIs that should be accessible during maintenance.
     *
     * @var array
     */
    protected static $neverPrevent = [];

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \ErrorException
     */
    public function handle($request, Closure $next)
    {
        if ($this->inExceptArray($request)) {
            return $next($request);
        }

        if ($this->app->maintenanceMode()->active()) {
            try {
                $data = $this->app->maintenanceMode()->data();
            } catch (ErrorException $exception) {
                if (! $this->app->maintenanceMode()->active()) {
                    return $next($request);
                }

                throw $exception;
            }

            if (isset($data['secret']) && $request->path() === $data['secret']) {
                return $this->bypassResponse($data['secret']);
            }

            if ($this->hasValidBypassCookie($request, $data)) {
                return $next($request);
            }

            if (isset($data['redirect'])) {
                $path = $data['redirect'] === '/'
                    ? $data['redirect']
                    : trim($data['redirect'], '/');

                if ($request->path() !== $path) {
                    return redirect($path);
                }
            }

            if (isset($data['template'])) {
                return response(
                    $data['template'],
                    $data['status'] ?? 503,
                    $this->getHeaders($data)
                );
            }

            throw new HttpException(
                $data['status'] ?? 503,
                'Service Unavailable',
                null,
                $this->getHeaders($data)
            );
        }

        return $next($request);
    }

    /**
     * Determine if the incoming request has a maintenance mode bypass cookie.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $data
     * @return bool
     */
    protected function hasValidBypassCookie($request, array $data)
    {
        return isset($data['secret']) &&
                $request->cookie('laravel_maintenance') &&
                MaintenanceModeBypassCookie::isValid(
                    $request->cookie('laravel_maintenance'),
                    $data['secret']
                );
    }

    /**
     * Redirect the user to their intended destination with a maintenance mode bypass cookie.
     *
     * @param  string  $secret
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function bypassResponse(string $secret)
    {
        return redirect()->intended('/')->withCookie(
            MaintenanceModeBypassCookie::create($secret)
        );
    }

    /**
     * Get the headers that should be sent with the response.
     *
     * @param  array  $data
     * @return array
     */
    protected function getHeaders($data)
    {
        $headers = isset($data['retry']) ? ['Retry-After' => $data['retry']] : [];

        if (isset($data['refresh'])) {
            $headers['Refresh'] = $data['refresh'];
        }

        return $headers;
    }

    /**
     * Get the URIs that should be excluded.
     *
     * @return array
     */
    public function getExcludedPaths()
    {
        return array_merge($this->except, static::$neverPrevent);
    }

    /**
     * Indicate that the given URIs should always be accessible.
     *
     * @param  array|string  $uris
     * @return void
     */
    public static function except($uris)
    {
        static::$neverPrevent = array_values(array_unique(
            array_merge(static::$neverPrevent, Arr::wrap($uris))
        ));
    }

    /**
     * Flush the state of the middleware.
     *
     * @return void
     */
    public static function flushState()
    {
        static::$neverPrevent = [];
    }
}
