<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\SuperAdminController;

class SuperAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && $request->user()->role === 'super_admin') {
            return $next($request);
        }

        return redirect('/'); // Redirect to home or login page if not authorized
    }
}
