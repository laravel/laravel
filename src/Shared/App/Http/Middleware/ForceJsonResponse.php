<?php

declare(strict_types=1);

namespace Lightit\Shared\App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceJsonResponse
{
    /**
     * @param Closure(Request): Response $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (
            $request->is('api/*')
            && ! $response instanceof JsonResponse
        ) {
            $response->headers->set('Accept', 'application/json');
            $response->headers->set('Content-Type', 'application/json');
        }

        return $response;
    }
}
