<?php

namespace App\Http\Middleware;

use Illuminate\Config\Repository as Config;
use Illuminate\Foundation\Http\Middleware\TrustHosts as Middleware;

class TrustHosts extends Middleware
{
    /**
     * The trusted host patterns.
     *
     * @var array
     */
    protected $trustedHosts = [];

    public function __construct(Config $config)
    {
        $this->trustedHosts[] = $config->get('app.trusted_host');

        // or maybe we can generate trusted hosts pattern from APP_URL
    }
}
