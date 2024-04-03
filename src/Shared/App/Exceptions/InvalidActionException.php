<?php

declare(strict_types=1);

namespace Lightit\Shared\App\Exceptions;

use Flugg\Responder\Exceptions\Http\HttpException;

class InvalidActionException extends HttpException
{
    /**
     * The HTTP status code.
     *
     * @var int
     */
    protected $status = 422;

    /**
     * The error code.
     *
     * @var string
     */
    protected $errorCode = 'invalid_action';

    /**
     * The error message.
     *
     * @var string
     */
    protected $message = 'This is an invalid action';
}
