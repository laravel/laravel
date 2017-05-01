<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The routes that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
    ];
}
