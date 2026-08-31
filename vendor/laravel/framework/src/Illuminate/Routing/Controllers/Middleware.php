<?php

namespace Illuminate\Routing\Controllers;

use Closure;
use Illuminate\Support\Arr;

class Middleware
{
    /**
     * Create a new controller middleware definition.
     *
     * @param  \Closure|string|array  $middleware
     * @param  array<string>|null  $only
     * @param  array<string>|null  $except
     */
    public function __construct(public Closure|string|array $middleware, public ?array $only = null, public ?array $except = null)
    {
    }

    /**
     * Specify the only controller methods the middleware should apply to.
     *
     * @param  array|string  $only
     * @return $this
     */
    public function only(array|string $only)
    {
        $this->only = Arr::wrap($only);

        return $this;
    }

    /**
     * Specify the controller methods the middleware should not apply to.
     *
     * @param  array|string  $except
     * @return $this
     */
    public function except(array|string $except)
    {
        $this->except = Arr::wrap($except);

        return $this;
    }
}
