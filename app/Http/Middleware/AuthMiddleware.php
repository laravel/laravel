<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Cek apakah user sudah login
        if (!Session::has('user')) {
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu!');
        }

        return $next($request);
    }
}
