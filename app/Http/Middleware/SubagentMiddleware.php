<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SubagentMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isSubagent()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        return $next($request);
    }
}
