<?php

namespace Illuminate\Routing\Middleware;

use Closure;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Support\Arr;

class ValidateSignature
{
    /**
     * The names of the parameters that should be ignored.
     *
     * @var array<int, string>
     */
    protected $ignore = [
        //
    ];

    /**
     * The globally ignored parameters.
     *
     * @var array
     */
    protected static $neverValidate = [];

    /**
     * Specify that the URL signature is for a relative URL.
     *
     * @param  array|string  $ignore
     * @return string
     */
    public static function relative($ignore = [])
    {
        $ignore = Arr::wrap($ignore);

        return static::class.':'.implode(',', empty($ignore) ? ['relative'] : ['relative',  ...$ignore]);
    }

    /**
     * Specify that the URL signature is for an absolute URL.
     *
     * @param  array|string  $ignore
     * @return class-string
     */
    public static function absolute($ignore = [])
    {
        $ignore = Arr::wrap($ignore);

        return empty($ignore)
            ? static::class
            : static::class.':'.implode(',', $ignore);
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  array|null  $args
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Routing\Exceptions\InvalidSignatureException
     */
    public function handle($request, Closure $next, ...$args)
    {
        [$relative, $ignore] = $this->parseArguments($args);

        if ($request->hasValidSignatureWhileIgnoring($ignore, ! $relative)) {
            return $next($request);
        }

        throw new InvalidSignatureException;
    }

    /**
     * Parse the additional arguments given to the middleware.
     *
     * @param  array  $args
     * @return array
     */
    protected function parseArguments(array $args)
    {
        $relative = ! empty($args) && $args[0] === 'relative';

        if ($relative) {
            array_shift($args);
        }

        $ignore = array_merge(
            property_exists($this, 'except') ? $this->except : $this->ignore,
            $args
        );

        return [$relative, array_merge($ignore, static::$neverValidate)];
    }

    /**
     * Indicate that the given parameters should be ignored during signature validation.
     *
     * @param  array|string  $parameters
     * @return void
     */
    public static function except($parameters)
    {
        static::$neverValidate = array_values(array_unique(
            array_merge(static::$neverValidate, Arr::wrap($parameters))
        ));
    }
}
