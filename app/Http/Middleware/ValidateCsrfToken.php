<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken as Middleware;

class ValidateCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF token validation.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
}
