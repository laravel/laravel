<?php

namespace Illuminate\Routing;

use BackedEnum;
use Closure;
use Illuminate\Contracts\Routing\UrlGenerator as UrlGeneratorContract;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\InteractsWithTime;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use InvalidArgumentException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class UrlGenerator implements UrlGeneratorContract
{
    use InteractsWithTime, Macroable;

    /**
     * The route collection.
     *
     * @var \Illuminate\Routing\RouteCollectionInterface
     */
    protected $routes;

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * The asset root URL.
     *
     * @var string
     */
    protected $assetRoot;

    /**
     * The forced URL root.
     *
     * @var string
     */
    protected $forcedRoot;

    /**
     * The forced scheme for URLs.
     *
     * @var string
     */
    protected $forceScheme;

    /**
     * A cached copy of the URL root for the current request.
     *
     * @var string|null
     */
    protected $cachedRoot;

    /**
     * A cached copy of the URL scheme for the current request.
     *
     * @var string|null
     */
    protected $cachedScheme;

    /**
     * The root namespace being applied to controller actions.
     *
     * @var string
     */
    protected $rootNamespace;

    /**
     * The session resolver callable.
     *
     * @var callable
     */
    protected $sessionResolver;

    /**
     * The encryption key resolver callable.
     *
     * @var callable
     */
    protected $keyResolver;

    /**
     * The missing named route resolver callable.
     *
     * @var callable
     */
    protected $missingNamedRouteResolver;

    /**
     * The callback to use to format hosts.
     *
     * @var \Closure
     */
    protected $formatHostUsing;

    /**
     * The callback to use to format paths.
     *
     * @var \Closure
     */
    protected $formatPathUsing;

    /**
     * The route URL generator instance.
     *
     * @var \Illuminate\Routing\RouteUrlGenerator|null
     */
    protected $routeGenerator;

    /**
     * Create a new URL Generator instance.
     *
     * @param  \Illuminate\Routing\RouteCollectionInterface  $routes
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $assetRoot
     */
    public function __construct(RouteCollectionInterface $routes, Request $request, $assetRoot = null)
    {
        $this->routes = $routes;
        $this->assetRoot = $assetRoot;

        $this->setRequest($request);
    }

    /**
     * Get the full URL for the current request.
     *
     * @return string
     */
    public function full()
    {
        return $this->request->fullUrl();
    }

    /**
     * Get the current URL for the request.
     *
     * @return string
     */
    public function current()
    {
        return $this->to($this->request->getPathInfo());
    }

    /**
     * Get the URL for the previous request.
     *
     * @param  mixed  $fallback
     * @return string
     */
    public function previous($fallback = false)
    {
        $referrer = $this->request->headers->get('referer');

        $url = $referrer ? $this->to($referrer) : $this->getPreviousUrlFromSession();

        if ($url) {
            return $url;
        } elseif ($fallback) {
            return $this->to($fallback);
        }

        return $this->to('/');
    }

    /**
     * Get the previous path info for the request.
     *
     * @param  mixed  $fallback
     * @return string
     */
    public function previousPath($fallback = false)
    {
        $previousPath = str_replace($this->to('/'), '', rtrim(preg_replace('/\?.*/', '', $this->previous($fallback)), '/'));

        return $previousPath === '' ? '/' : $previousPath;
    }

    /**
     * Get the previous URL from the session if possible.
     *
     * @return string|null
     */
    protected function getPreviousUrlFromSession()
    {
        return $this->getSession()?->previousUrl();
    }

    /**
     * Generate an absolute URL to the given path.
     *
     * @param  string  $path
     * @param  mixed  $extra
     * @param  bool|null  $secure
     * @return string
     */
    public function to($path, $extra = [], $secure = null)
    {
        // First we will check if the URL is already a valid URL. If it is we will not
        // try to generate a new one but will simply return the URL as is, which is
        // convenient since developers do not always have to check if it's valid.
        if ($this->isValidUrl($path)) {
            return $path;
        }

        $tail = implode('/', array_map(
            'rawurlencode', (array) $this->formatParameters($extra))
        );

        // Once we have the scheme we will compile the "tail" by collapsing the values
        // into a single string delimited by slashes. This just makes it convenient
        // for passing the array of parameters to this URL as a list of segments.
        $root = $this->formatRoot($this->formatScheme($secure));

        [$path, $query] = $this->extractQueryString($path);

        return $this->format(
            $root, '/'.trim($path.'/'.$tail, '/')
        ).$query;
    }

    /**
     * Generate an absolute URL with the given query parameters.
     *
     * @param  string  $path
     * @param  array  $query
     * @param  mixed  $extra
     * @param  bool|null  $secure
     * @return string
     */
    public function query($path, $query = [], $extra = [], $secure = null)
    {
        [$path, $existingQueryString] = $this->extractQueryString($path);

        parse_str(Str::after($existingQueryString, '?'), $existingQueryArray);

        return rtrim($this->to($path.'?'.Arr::query(
            array_merge($existingQueryArray, $query)
        ), $extra, $secure), '?');
    }

    /**
     * Generate a secure, absolute URL to the given path.
     *
     * @param  string  $path
     * @param  array  $parameters
     * @return string
     */
    public function secure($path, $parameters = [])
    {
        return $this->to($path, $parameters, true);
    }

    /**
     * Generate the URL to an application asset.
     *
     * @param  string  $path
     * @param  bool|null  $secure
     * @return string
     */
    public function asset($path, $secure = null)
    {
        if ($this->isValidUrl($path)) {
            return $path;
        }

        // Once we get the root URL, we will check to see if it contains an index.php
        // file in the paths. If it does, we will remove it since it is not needed
        // for asset paths, but only for routes to endpoints in the application.
        $root = $this->assetRoot ?: $this->formatRoot($this->formatScheme($secure));

        return Str::finish($this->removeIndex($root), '/').trim($path, '/');
    }

    /**
     * Generate the URL to a secure asset.
     *
     * @param  string  $path
     * @return string
     */
    public function secureAsset($path)
    {
        return $this->asset($path, true);
    }

    /**
     * Generate the URL to an asset from a custom root domain such as CDN, etc.
     *
     * @param  string  $root
     * @param  string  $path
     * @param  bool|null  $secure
     * @return string
     */
    public function assetFrom($root, $path, $secure = null)
    {
        // Once we get the root URL, we will check to see if it contains an index.php
        // file in the paths. If it does, we will remove it since it is not needed
        // for asset paths, but only for routes to endpoints in the application.
        $root = $this->formatRoot($this->formatScheme($secure), $root);

        return $this->removeIndex($root).'/'.trim($path, '/');
    }

    /**
     * Remove the index.php file from a path.
     *
     * @param  string  $root
     * @return string
     */
    protected function removeIndex($root)
    {
        $i = 'index.php';

        return str_contains($root, $i) ? str_replace('/'.$i, '', $root) : $root;
    }

    /**
     * Get the default scheme for a raw URL.
     *
     * @param  bool|null  $secure
     * @return string
     */
    public function formatScheme($secure = null)
    {
        if (! is_null($secure)) {
            return $secure ? 'https://' : 'http://';
        }

        if (is_null($this->cachedScheme)) {
            $this->cachedScheme = $this->forceScheme ?: $this->request->getScheme().'://';
        }

        return $this->cachedScheme;
    }

    /**
     * Create a signed route URL for a named route.
     *
     * @param  \BackedEnum|string  $name
     * @param  mixed  $parameters
     * @param  \DateTimeInterface|\DateInterval|int|null  $expiration
     * @param  bool  $absolute
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function signedRoute($name, $parameters = [], $expiration = null, $absolute = true)
    {
        $this->ensureSignedRouteParametersAreNotReserved(
            $parameters = Arr::wrap($parameters)
        );

        if ($expiration) {
            $parameters = $parameters + ['expires' => $this->availableAt($expiration)];
        }

        ksort($parameters);

        $key = call_user_func($this->keyResolver);

        return $this->route($name, $parameters + [
            'signature' => hash_hmac(
                'sha256',
                $this->route($name, $parameters, $absolute),
                is_array($key) ? $key[0] : $key
            ),
        ], $absolute);
    }

    /**
     * Ensure the given signed route parameters are not reserved.
     *
     * @param  mixed  $parameters
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    protected function ensureSignedRouteParametersAreNotReserved($parameters)
    {
        if (array_key_exists('signature', $parameters)) {
            throw new InvalidArgumentException(
                '"Signature" is a reserved parameter when generating signed routes. Please rename your route parameter.'
            );
        }

        if (array_key_exists('expires', $parameters)) {
            throw new InvalidArgumentException(
                '"Expires" is a reserved parameter when generating signed routes. Please rename your route parameter.'
            );
        }
    }

    /**
     * Create a temporary signed route URL for a named route.
     *
     * @param  \BackedEnum|string  $name
     * @param  \DateTimeInterface|\DateInterval|int  $expiration
     * @param  array  $parameters
     * @param  bool  $absolute
     * @return string
     */
    public function temporarySignedRoute($name, $expiration, $parameters = [], $absolute = true)
    {
        return $this->signedRoute($name, $parameters, $expiration, $absolute);
    }

    /**
     * Determine if the given request has a valid signature.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  bool  $absolute
     * @param  \Closure|array  $ignoreQuery
     * @return bool
     */
    public function hasValidSignature(Request $request, $absolute = true, Closure|array $ignoreQuery = [])
    {
        return $this->hasCorrectSignature($request, $absolute, $ignoreQuery)
            && $this->signatureHasNotExpired($request);
    }

    /**
     * Determine if the given request has a valid signature for a relative URL.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure|array  $ignoreQuery
     * @return bool
     */
    public function hasValidRelativeSignature(Request $request, Closure|array $ignoreQuery = [])
    {
        return $this->hasValidSignature($request, false, $ignoreQuery);
    }

    /**
     * Determine if the signature from the given request matches the URL.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  bool  $absolute
     * @param  \Closure|array  $ignoreQuery
     * @return bool
     */
    public function hasCorrectSignature(Request $request, $absolute = true, Closure|array $ignoreQuery = [])
    {
        $url = $absolute ? $request->url() : '/'.$request->path();

        $queryString = (new Collection(explode('&', (string) $request->server->get('QUERY_STRING'))))
            ->reject(function ($parameter) use ($ignoreQuery) {
                $parameter = Str::before($parameter, '=');

                if ($parameter === 'signature') {
                    return true;
                }

                if ($ignoreQuery instanceof Closure) {
                    return $ignoreQuery($parameter);
                }

                return in_array($parameter, $ignoreQuery);
            })
            ->join('&');

        $original = rtrim($url.'?'.$queryString, '?');

        $keys = call_user_func($this->keyResolver);

        $keys = is_array($keys) ? $keys : [$keys];

        $signature = $request->query('signature');

        if (! is_string($signature)) {
            return false;
        }

        foreach ($keys as $key) {
            if (hash_equals(
                hash_hmac('sha256', $original, $key),
                $signature
            )) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the expires timestamp from the given request is not from the past.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function signatureHasNotExpired(Request $request)
    {
        $expires = $request->query('expires');

        return ! ($expires && Carbon::now()->getTimestamp() > $expires);
    }

    /**
     * Get the URL to a named route.
     *
     * @param  \BackedEnum|string  $name
     * @param  mixed  $parameters
     * @param  bool  $absolute
     * @return string
     *
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException|\InvalidArgumentException
     */
    public function route($name, $parameters = [], $absolute = true)
    {
        if ($name instanceof BackedEnum && ! is_string($name = $name->value)) {
            throw new InvalidArgumentException('Attribute [name] expects a string backed enum.');
        }

        if (! is_null($route = $this->routes->getByName($name))) {
            return $this->toRoute($route, $parameters, $absolute);
        }

        if (! is_null($this->missingNamedRouteResolver) &&
            ! is_null($url = call_user_func($this->missingNamedRouteResolver, $name, $parameters, $absolute))) {
            return $url;
        }

        throw new RouteNotFoundException("Route [{$name}] not defined.");
    }

    /**
     * Get the URL for a given route instance.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @param  mixed  $parameters
     * @param  bool  $absolute
     * @return string
     *
     * @throws \Illuminate\Routing\Exceptions\UrlGenerationException
     */
    public function toRoute($route, $parameters, $absolute)
    {
        return $this->routeUrl()->to(
            $route, $parameters, $absolute
        );
    }

    /**
     * Get the URL to a controller action.
     *
     * @param  string|array  $action
     * @param  mixed  $parameters
     * @param  bool  $absolute
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function action($action, $parameters = [], $absolute = true)
    {
        if (is_null($route = $this->routes->getByAction($action = $this->formatAction($action)))) {
            throw new InvalidArgumentException("Action {$action} not defined.");
        }

        return $this->toRoute($route, $parameters, $absolute);
    }

    /**
     * Format the given controller action.
     *
     * @param  string|array  $action
     * @return string
     */
    protected function formatAction($action)
    {
        if (is_array($action)) {
            $action = '\\'.implode('@', $action);
        }

        if ($this->rootNamespace && ! str_starts_with($action, '\\')) {
            return $this->rootNamespace.'\\'.$action;
        }

        return trim($action, '\\');
    }

    /**
     * Format the array of URL parameters.
     *
     * @param  mixed  $parameters
     * @return array
     */
    public function formatParameters($parameters)
    {
        $parameters = Arr::wrap($parameters);

        foreach ($parameters as $key => $parameter) {
            if ($parameter instanceof UrlRoutable) {
                $parameters[$key] = $parameter->getRouteKey();
            }
        }

        return $parameters;
    }

    /**
     * Extract the query string from the given path.
     *
     * @param  string  $path
     * @return array
     */
    protected function extractQueryString($path)
    {
        if (($queryPosition = strpos($path, '?')) !== false) {
            return [
                substr($path, 0, $queryPosition),
                substr($path, $queryPosition),
            ];
        }

        return [$path, ''];
    }

    /**
     * Get the base URL for the request.
     *
     * @param  string  $scheme
     * @param  string|null  $root
     * @return string
     */
    public function formatRoot($scheme, $root = null)
    {
        if (is_null($root)) {
            if (is_null($this->cachedRoot)) {
                $this->cachedRoot = $this->forcedRoot ?: $this->request->root();
            }

            $root = $this->cachedRoot;
        }

        $start = str_starts_with($root, 'http://') ? 'http://' : 'https://';

        return preg_replace('~'.$start.'~', $scheme, $root, 1);
    }

    /**
     * Format the given URL segments into a single URL.
     *
     * @param  string  $root
     * @param  string  $path
     * @param  \Illuminate\Routing\Route|null  $route
     * @return string
     */
    public function format($root, $path, $route = null)
    {
        $path = '/'.trim($path, '/');

        if ($this->formatHostUsing) {
            $root = call_user_func($this->formatHostUsing, $root, $route);
        }

        if ($this->formatPathUsing) {
            $path = call_user_func($this->formatPathUsing, $path, $route);
        }

        return trim($root.$path, '/');
    }

    /**
     * Determine if the given path is a valid URL.
     *
     * @param  string  $path
     * @return bool
     */
    public function isValidUrl($path)
    {
        if (! preg_match('~^(#|//|https?://|(mailto|tel|sms):)~', $path)) {
            return filter_var($path, FILTER_VALIDATE_URL) !== false;
        }

        return true;
    }

    /**
     * Get the Route URL generator instance.
     *
     * @return \Illuminate\Routing\RouteUrlGenerator
     */
    protected function routeUrl()
    {
        if (! $this->routeGenerator) {
            $this->routeGenerator = new RouteUrlGenerator($this, $this->request);
        }

        return $this->routeGenerator;
    }

    /**
     * Set the default named parameters used by the URL generator.
     *
     * @param  array  $defaults
     * @return void
     */
    public function defaults(array $defaults)
    {
        $this->routeUrl()->defaults($defaults);
    }

    /**
     * Get the default named parameters used by the URL generator.
     *
     * @return array
     */
    public function getDefaultParameters()
    {
        return $this->routeUrl()->defaultParameters;
    }

    /**
     * Force the scheme for URLs.
     *
     * @param  string|null  $scheme
     * @return void
     */
    public function forceScheme($scheme)
    {
        $this->cachedScheme = null;

        $this->forceScheme = $scheme ? $scheme.'://' : null;
    }

    /**
     * Force the use of the HTTPS scheme for all generated URLs.
     *
     * @param  bool  $force
     * @return void
     */
    public function forceHttps($force = true)
    {
        if ($force) {
            $this->forceScheme('https');
        }
    }

    /**
     * Set the URL origin for all generated URLs.
     *
     * @param  string|null  $root
     * @return void
     */
    public function useOrigin(?string $root)
    {
        $this->forceRootUrl($root);
    }

    /**
     * Set the forced root URL.
     *
     * @param  string|null  $root
     * @return void
     *
     * @deprecated Use useOrigin
     */
    public function forceRootUrl($root)
    {
        $this->forcedRoot = $root ? rtrim($root, '/') : null;

        $this->cachedRoot = null;
    }

    /**
     * Set the URL origin for all generated asset URLs.
     *
     * @param  string|null  $root
     * @return void
     */
    public function useAssetOrigin(?string $root)
    {
        $this->assetRoot = $root ? rtrim($root, '/') : null;
    }

    /**
     * Set a callback to be used to format the host of generated URLs.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function formatHostUsing(Closure $callback)
    {
        $this->formatHostUsing = $callback;

        return $this;
    }

    /**
     * Set a callback to be used to format the path of generated URLs.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function formatPathUsing(Closure $callback)
    {
        $this->formatPathUsing = $callback;

        return $this;
    }

    /**
     * Get the path formatter being used by the URL generator.
     *
     * @return \Closure
     */
    public function pathFormatter()
    {
        return $this->formatPathUsing ?: function ($path) {
            return $path;
        };
    }

    /**
     * Get the request instance.
     *
     * @return \Illuminate\Http\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set the current request instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        $this->cachedRoot = null;
        $this->cachedScheme = null;

        tap($this->routeGenerator?->defaultParameters ?: [], function ($defaults) {
            $this->routeGenerator = null;

            if (! empty($defaults)) {
                $this->defaults($defaults);
            }
        });
    }

    /**
     * Set the route collection.
     *
     * @param  \Illuminate\Routing\RouteCollectionInterface  $routes
     * @return $this
     */
    public function setRoutes(RouteCollectionInterface $routes)
    {
        $this->routes = $routes;

        return $this;
    }

    /**
     * Get the session implementation from the resolver.
     *
     * @return \Illuminate\Session\Store|null
     */
    protected function getSession()
    {
        if ($this->sessionResolver) {
            return call_user_func($this->sessionResolver);
        }
    }

    /**
     * Set the session resolver for the generator.
     *
     * @param  callable  $sessionResolver
     * @return $this
     */
    public function setSessionResolver(callable $sessionResolver)
    {
        $this->sessionResolver = $sessionResolver;

        return $this;
    }

    /**
     * Set the encryption key resolver.
     *
     * @param  callable  $keyResolver
     * @return $this
     */
    public function setKeyResolver(callable $keyResolver)
    {
        $this->keyResolver = $keyResolver;

        return $this;
    }

    /**
     * Clone a new instance of the URL generator with a different encryption key resolver.
     *
     * @param  callable  $keyResolver
     * @return \Illuminate\Routing\UrlGenerator
     */
    public function withKeyResolver(callable $keyResolver)
    {
        return (clone $this)->setKeyResolver($keyResolver);
    }

    /**
     * Set the callback that should be used to attempt to resolve missing named routes.
     *
     * @param  callable  $missingNamedRouteResolver
     * @return $this
     */
    public function resolveMissingNamedRoutesUsing(callable $missingNamedRouteResolver)
    {
        $this->missingNamedRouteResolver = $missingNamedRouteResolver;

        return $this;
    }

    /**
     * Get the root controller namespace.
     *
     * @return string
     */
    public function getRootControllerNamespace()
    {
        return $this->rootNamespace;
    }

    /**
     * Set the root controller namespace.
     *
     * @param  string  $rootNamespace
     * @return $this
     */
    public function setRootControllerNamespace($rootNamespace)
    {
        $this->rootNamespace = $rootNamespace;

        return $this;
    }
}
