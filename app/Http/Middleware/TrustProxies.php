<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Fideloper\Proxy\TrustProxies as Middleware;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var null|string|array
     */
    protected $proxies;

    /**
     * The headers that should be used to detect proxies.
     *
     * @var null|string|int
     */
    protected $headers;
}
