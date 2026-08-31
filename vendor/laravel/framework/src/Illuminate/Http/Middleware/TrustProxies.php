<?php

namespace Illuminate\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TrustProxies
{
    /**
     * The trusted proxies for the application.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies;

    /**
     * The trusted proxies headers for the application.
     *
     * @var int
     */
    protected $headers = Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_PREFIX |
        Request::HEADER_X_FORWARDED_AWS_ELB;

    /**
     * The proxies that have been configured to always be trusted.
     *
     * @var array<int, string>|string|null
     */
    protected static $alwaysTrustProxies;

    /**
     * The proxies headers that have been configured to always be trusted.
     *
     * @var int|null
     */
    protected static $alwaysTrustHeaders;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function handle(Request $request, Closure $next)
    {
        $request::setTrustedProxies([], $this->getTrustedHeaderNames());

        $this->setTrustedProxyIpAddresses($request);

        return $next($request);
    }

    /**
     * Sets the trusted proxies on the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function setTrustedProxyIpAddresses(Request $request)
    {
        $trustedIps = $this->proxies() ?: config('trustedproxy.proxies');

        if (is_null($trustedIps) &&
            (laravel_cloud() ||
             str_ends_with($request->host(), '.on-forge.com') ||
             str_ends_with($request->host(), '.on-vapor.com'))) {
            $trustedIps = '*';
        }

        if (str_ends_with($request->host(), '.on-forge.com') ||
            str_ends_with($request->host(), '.on-vapor.com')) {
            $request->headers->remove('X-Forwarded-Host');
        }

        if ($trustedIps === '*' || $trustedIps === '**') {
            return $this->setTrustedProxyIpAddressesToTheCallingIp($request);
        }

        $trustedIps = is_string($trustedIps)
            ? array_map(trim(...), explode(',', $trustedIps))
            : $trustedIps;

        if (is_array($trustedIps)) {
            return $this->setTrustedProxyIpAddressesToSpecificIps($request, $trustedIps);
        }
    }

    /**
     * Specify the IP addresses to trust explicitly.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $trustedIps
     * @return void
     */
    protected function setTrustedProxyIpAddressesToSpecificIps(Request $request, array $trustedIps)
    {
        $request->setTrustedProxies(array_reduce($trustedIps, function ($ips, $trustedIp) use ($request) {
            $ips[] = $trustedIp === 'REMOTE_ADDR'
                ? $request->server->get('REMOTE_ADDR')
                : $trustedIp;

            return $ips;
        }, []), $this->getTrustedHeaderNames());
    }

    /**
     * Set the trusted proxy to be the IP address calling this servers.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function setTrustedProxyIpAddressesToTheCallingIp(Request $request)
    {
        $request->setTrustedProxies([$request->server->get('REMOTE_ADDR')], $this->getTrustedHeaderNames());
    }

    /**
     * Retrieve trusted header name(s), falling back to defaults if config not set.
     *
     * @return int A bit field of Request::HEADER_*, to set which headers to trust from your proxies.
     */
    protected function getTrustedHeaderNames()
    {
        $headers = $this->headers();

        if (is_int($headers)) {
            return $headers;
        }

        return match ($headers) {
            'HEADER_X_FORWARDED_AWS_ELB' => Request::HEADER_X_FORWARDED_AWS_ELB,
            'HEADER_FORWARDED' => Request::HEADER_FORWARDED,
            'HEADER_X_FORWARDED_FOR' => Request::HEADER_X_FORWARDED_FOR,
            'HEADER_X_FORWARDED_HOST' => Request::HEADER_X_FORWARDED_HOST,
            'HEADER_X_FORWARDED_PORT' => Request::HEADER_X_FORWARDED_PORT,
            'HEADER_X_FORWARDED_PROTO' => Request::HEADER_X_FORWARDED_PROTO,
            'HEADER_X_FORWARDED_PREFIX' => Request::HEADER_X_FORWARDED_PREFIX,
            default => Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO | Request::HEADER_X_FORWARDED_PREFIX | Request::HEADER_X_FORWARDED_AWS_ELB,
        };
    }

    /**
     * Get the trusted headers.
     *
     * @return int
     */
    protected function headers()
    {
        return static::$alwaysTrustHeaders ?: $this->headers;
    }

    /**
     * Get the trusted proxies.
     *
     * @return array|string|null
     */
    protected function proxies()
    {
        return static::$alwaysTrustProxies ?: $this->proxies;
    }

    /**
     * Specify the IP addresses of proxies that should always be trusted.
     *
     * @param  array|string  $proxies
     * @return void
     */
    public static function at(array|string $proxies)
    {
        static::$alwaysTrustProxies = $proxies;
    }

    /**
     * Specify the proxy headers that should always be trusted.
     *
     * @param  int  $headers
     * @return void
     */
    public static function withHeaders(int $headers)
    {
        static::$alwaysTrustHeaders = $headers;
    }

    /**
     * Flush the state of the middleware.
     *
     * @return void
     */
    public static function flushState()
    {
        static::$alwaysTrustHeaders = null;
        static::$alwaysTrustProxies = null;
    }
}
