<?php

declare(strict_types=1);

namespace Lightit\Shared\App\Exceptions\Http;

class RelationNotFoundException extends HttpException
{
    /**
     * An HTTP status code.
     */
    protected int $status = 422;

    /**
     * An error code.
     */
    protected string|null $errorCode = 'relation_not_found';
}
