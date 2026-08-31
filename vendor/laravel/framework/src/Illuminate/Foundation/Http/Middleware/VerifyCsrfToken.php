<?php

namespace Illuminate\Foundation\Http\Middleware;

use Closure;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Cookie\CookieValuePrefix;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\Concerns\ExcludesPaths;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Arr;
use Illuminate\Support\InteractsWithTime;
use Symfony\Component\HttpFoundation\Cookie;

class VerifyCsrfToken
{
    use InteractsWithTime,
        ExcludesPaths;

    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The encrypter implementation.
     *
     * @var \Illuminate\Contracts\Encryption\Encrypter
     */
    protected $encrypter;

    /**
     * The URIs that should be excluded.
     *
     * @var array<int, string>
     */
    protected $except = [];

    /**
     * The globally ignored URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected static $neverVerify = [];

    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  \Illuminate\Contracts\Encryption\Encrypter  $encrypter
     */
    public function __construct(Application $app, Encrypter $encrypter)
    {
        $this->app = $app;
        $this->encrypter = $encrypter;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Illuminate\Session\TokenMismatchException
     */
    public function handle($request, Closure $next)
    {
        if (
            $this->isReading($request) ||
            $this->runningUnitTests() ||
            $this->inExceptArray($request) ||
            $this->tokensMatch($request)
        ) {
            return tap($next($request), function ($response) use ($request) {
                if ($this->shouldAddXsrfTokenCookie()) {
                    $this->addCookieToResponse($request, $response);
                }
            });
        }

        throw new TokenMismatchException('CSRF token mismatch.');
    }

    /**
     * Determine if the HTTP request uses a ‘read’ verb.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function isReading($request)
    {
        return in_array($request->method(), ['HEAD', 'GET', 'OPTIONS']);
    }

    /**
     * Determine if the application is running unit tests.
     *
     * @return bool
     */
    protected function runningUnitTests()
    {
        return $this->app->runningInConsole() && $this->app->runningUnitTests();
    }

    /**
     * Get the URIs that should be excluded.
     *
     * @return array
     */
    public function getExcludedPaths()
    {
        return array_merge($this->except, static::$neverVerify);
    }

    /**
     * Determine if the session and input CSRF tokens match.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function tokensMatch($request)
    {
        $token = $this->getTokenFromRequest($request);

        return is_string($request->session()->token()) &&
               is_string($token) &&
               hash_equals($request->session()->token(), $token);
    }

    /**
     * Get the CSRF token from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function getTokenFromRequest($request)
    {
        $token = $request->input('_token') ?: $request->header('X-CSRF-TOKEN');

        if (! $token && $header = $request->header('X-XSRF-TOKEN')) {
            try {
                $token = CookieValuePrefix::remove($this->encrypter->decrypt($header, static::serialized()));
            } catch (DecryptException) {
                $token = '';
            }
        }

        return $token;
    }

    /**
     * Determine if the cookie should be added to the response.
     *
     * @return bool
     */
    public function shouldAddXsrfTokenCookie()
    {
        return $this->addHttpCookie;
    }

    /**
     * Add the CSRF token to the response cookies.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function addCookieToResponse($request, $response)
    {
        $config = config('session');

        if ($response instanceof Responsable) {
            $response = $response->toResponse($request);
        }

        $response->headers->setCookie($this->newCookie($request, $config));

        return $response;
    }

    /**
     * Create a new "XSRF-TOKEN" cookie that contains the CSRF token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $config
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    protected function newCookie($request, $config)
    {
        return new Cookie(
            'XSRF-TOKEN',
            $request->session()->token(),
            $this->availableAt(60 * $config['lifetime']),
            $config['path'],
            $config['domain'],
            $config['secure'],
            false,
            false,
            $config['same_site'] ?? null,
            $config['partitioned'] ?? false
        );
    }

    /**
     * Indicate that the given URIs should be excluded from CSRF verification.
     *
     * @param  array|string  $uris
     * @return void
     */
    public static function except($uris)
    {
        static::$neverVerify = array_values(array_unique(
            array_merge(static::$neverVerify, Arr::wrap($uris))
        ));
    }

    /**
     * Determine if the cookie contents should be serialized.
     *
     * @return bool
     */
    public static function serialized()
    {
        return EncryptCookies::serialized('XSRF-TOKEN');
    }

    /**
     * Flush the state of the middleware.
     *
     * @return void
     */
    public static function flushState()
    {
        static::$neverVerify = [];
    }
}
