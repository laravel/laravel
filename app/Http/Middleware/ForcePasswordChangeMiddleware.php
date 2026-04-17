<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChangeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->must_change_password) {
            return $next($request);
        }

        if ($request->routeIs('change-password') || $request->routeIs('change-password.update') || $request->routeIs('logout')) {
            return $next($request);
        }

        return redirect()->route('change-password')
            ->with('warning', 'Silakan ubah password sementara Anda sebelum melanjutkan.');
    }
}
