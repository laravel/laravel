<?php

namespace App\Http\Middleware;

use Closure;

class ValidateJson
{
    /**
     * The HTTP verbs that should be validated.
     *
     * @var array
     */
    protected $methodsToParse = [
        'DELETE',
        'PATCH',
        'POST',
        'PUT',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! in_array($request->getMethod(), $this->methodsToParse)) {
            return $next($request);
        }

        json_decode($request->getContent());

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \RuntimeException(
                'Unable to parse JSON data: '
                .json_last_error_msg()
            );
        }

        return $next($request);
    }
}
