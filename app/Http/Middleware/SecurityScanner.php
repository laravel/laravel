<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SecurityScanner
{
    public function handle(Request $request, Closure $next)
    {
        // فحص SQL Injection
        $input = $request->all();
        foreach ($input as $key => $value) {
            if ($this->containsSQLInjection($value)) {
                Log::warning('Potential SQL Injection detected in field: ' . $key);
                abort(403, 'Potential SQL Injection detected in field: ' . $key);
            }
        }

        // فحص XSS
        foreach ($input as $key => $value) {
            if ($this->containsXSS($value)) {
                Log::warning('Potential XSS attack detected in field: ' . $key);
                abort(403, 'Potential XSS attack detected in field: ' . $key);
            }
        }

        // فحص CSRF
        if ($request->isMethod('post') && !$request->has('_token')) {
            abort(403, 'CSRF token missing.');
        }

        return $next($request);
    }

    protected function containsSQLInjection($input)
    {
        $pattern = '/(union|select|insert|update|delete|drop|alter|create|exec|from|where|having|;|--)/i';
        return preg_match($pattern, $input);
    }

    protected function containsXSS($input)
    {
        return preg_match('/<[^>]+>/', $input); // تحقق من وجود أي تاغات HTML
    }
}
