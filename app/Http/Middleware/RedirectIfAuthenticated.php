<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\RedirectIfAuthenticated as Middleware;
use Illuminate\Http\Request;

class RedirectIfAuthenticated extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return route('dashboard');
    }
}
