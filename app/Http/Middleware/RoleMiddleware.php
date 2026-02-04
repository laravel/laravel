<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $roles)
    {
        $rolesArray = is_array($roles) ? $roles : explode('|', $roles);

        if (!auth()->check() || !in_array(auth()->user()->role, $rolesArray, true)) {
            abort(403);
        }

        return $next($request);
    }
}
