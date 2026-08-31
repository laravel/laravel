<?php

namespace Illuminate\Cache\RateLimiting;

class Unlimited extends GlobalLimit
{
    /**
     * Create a new limit instance.
     */
    public function __construct()
    {
        parent::__construct(PHP_INT_MAX);
    }
}
