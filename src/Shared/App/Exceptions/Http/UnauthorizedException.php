<?php

declare(strict_types=1);

namespace Lightit\Shared\App\Exceptions\Http;

class UnauthorizedException extends HttpException
{
    /**
     * An HTTP status code.
     */
    protected int $status = 401;

    /**
     * An error code.
     */
    protected string|null $errorCode = 'unauthorized';
}
