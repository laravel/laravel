<?php

namespace Illuminate\Http\Exceptions;

use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class HttpResponseException extends RuntimeException
{
    /**
     * The underlying response instance.
     *
     * @var \Symfony\Component\HttpFoundation\Response
     */
    protected $response;

    /**
     * Create a new HTTP response exception instance.
     *
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @param  \Throwable|null  $previous
     */
    public function __construct(Response $response, ?Throwable $previous = null)
    {
        parent::__construct($previous?->getMessage() ?? '', $previous?->getCode() ?? 0, $previous);

        $this->response = $response;
    }

    /**
     * Get the underlying response instance.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
