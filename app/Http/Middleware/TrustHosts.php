<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrustHosts as Middleware;

class TrustHosts extends Middleware
{
    /**
     * The trusted host names.
     * 
     * @var array
     */
    protected $trustedHosts = [];
}
