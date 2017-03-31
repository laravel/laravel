<?php

namespace App\Http\Middleware;

use Closure;

class AddCSPHeader
{
    /**
     * A Content Security Policy (CSP) header can limit the impact of XSS attacks.
     * Resources: https://content-security-policy.com/ or https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP
     * Video tutorial: https://www.troyhunt.com/understanding-csp-the-video-tutorial-edition/
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        return $response->header(
          'Content-Security-Policy',
          "default-src 'self';".
          "script-src 'self' 'unsafe-eval' 'unsafe-inline';". // unsafe-eval is needed for Vue.js
          "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com;".
          "font-src 'self' https://fonts.gstatic.com;"
        );
    }
}
