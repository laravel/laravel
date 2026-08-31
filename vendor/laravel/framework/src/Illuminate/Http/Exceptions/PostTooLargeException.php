<?php

namespace Illuminate\Http\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class PostTooLargeException extends HttpException
{
    /**
     * Create a new "post too large" exception instance.
     *
     * @param  string  $message
     * @param  \Throwable|null  $previous
     * @param  array  $headers
     * @param  int  $code
     */
    public function __construct($message = '', ?Throwable $previous = null, array $headers = [], $code = 0)
    {
        parent::__construct(413, $message, $previous, $headers, $code);
    }
}
