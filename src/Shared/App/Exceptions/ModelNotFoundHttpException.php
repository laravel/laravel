<?php

declare(strict_types=1);

namespace Lightit\Shared\App\Exceptions;

use Flugg\Responder\Exceptions\Http\HttpException;

class ModelNotFoundHttpException extends HttpException
{
    /**
     * An HTTP status code.
     *
     * @var int
     */
    protected $status = 404;

    /**
     * An error code.
     *
     * @var string|null
     */
    protected $errorCode = 'entity_not_found';
}
