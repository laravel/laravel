<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Fideloper\Proxy\TrustProxies as Middleware;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array
     */
    protected $proxies;

    /**
     * The current proxy header mappings.
     *
     * @var array
     */
    protected $headers = [
        Request::HEADER_FORWARDED => 'FORWARDED',
        Request::HEADER_CLIENT_IP => 'X_FORWARDED_FOR',
        Request::HEADER_CLIENT_HOST => 'X_FORWARDED_HOST',
        Request::HEADER_CLIENT_PORT => 'X_FORWARDED_PORT',
        Request::HEADER_CLIENT_PROTO => 'X_FORWARDED_PROTO',
    ];
}
