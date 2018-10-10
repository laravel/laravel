<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Contracts\Config\Repository;
use Fideloper\Proxy\TrustProxies as Middleware;

class TrustProxies extends Middleware
{
    /**
     * Create a new trusted proxies middleware instance.
     *
     * @param \Illuminate\Contracts\Config\Repository $config
     */
    public function __construct(Repository $config)
    {
        parent::__construct($config);

        $this->setProxiesFromConfig();
        $this->setHeadersFromConfig();
    }

    /**
     * Set Proxies from config. The trusted proxies for this application.
     */
    private function setProxiesFromConfig()
    {
        $this->proxies = null;
        $config = $this->config->get('trustedproxy.proxies');
        if ($config === '*' || $config === '**' || is_array($config)) {
            $this->proxies = $config;
        } elseif ($config && is_string($config)) {
            $this->proxies = explode(',', $config);
        }
    }

    /**
     * Set Headers from config and check valid values. The headers that should be used to detect proxies.
     */
    private function setHeadersFromConfig()
    {
        $this->headers = Request::HEADER_X_FORWARDED_ALL;
        $config = $this->config->get('trustedproxy.headers');
        if ($config === 'HEADER_X_FORWARDED_AWS_ELB') {
            $this->headers = Request::HEADER_X_FORWARDED_AWS_ELB;
        } elseif ($config === 'HEADER_FORWARDED') {
            $this->headers = Request::HEADER_FORWARDED;
        }
    }
}
