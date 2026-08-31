<?php

namespace Illuminate\Http\Exceptions;

use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;

class ThrottleRequestsException extends TooManyRequestsHttpException
{
    /**
     * Create a new throttle requests exception instance.
     *
     * @param  string  $message
     * @param  \Throwable|null  $previous
     * @param  array  $headers
     * @param  int  $code
     */
    public function __construct($message = '', ?Throwable $previous = null, array $headers = [], $code = 0)
    {
        parent::__construct(null, $message, $previous, $code, $headers);
    }
}
