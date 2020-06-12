<?php

namespace App\Exceptions;

use Flugg\Responder\Exceptions\Http\HttpException;
use Illuminate\Http\Response;

class PageNotFoundException extends HttpException
{
    /**
     * An HTTP status code.
     *
     * @var int
     */
    protected $status = Response::HTTP_NOT_FOUND;

    /**
     * An error code.
     *
     * @var string|null
     */
    protected $errorCode = 'PAGE_NOT_FOUND';
}
