<?php

declare(strict_types=1);

namespace Lightit\Shared\App\Exceptions\Http;

use Illuminate\Contracts\Validation\Validator;

class ValidationFailedException extends HttpException
{
    /**
     * An HTTP status code.
     */
    protected int $status = 422;

    /**
     * An error code.
     */
    protected string|null $errorCode = 'validation_failed';

    public function __construct(string $message, protected Validator $validator)
    {
        parent::__construct($message, $this->headers);
    }

    /**
     * Retrieve the error data.
     */
    public function data(): array|null
    {
        return ['fields' => $this->validator->getMessageBag()->toArray()];
    }
}
