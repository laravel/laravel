<?php

declare(strict_types=1);

namespace Lightit\Shared\App\Exceptions\Http;

use Symfony\Component\HttpKernel\Exception\HttpException as BaseHttpException;

abstract class HttpException extends BaseHttpException
{
    /**
     * An HTTP status code.
     */
    protected int $status = 500;

    /**
     * An error code.
     */
    protected string|null $errorCode = null;

    /**
     * An error message.
     *
     * @var string
     */
    protected $message = '';

    /**
     * Additional error data.
     */
    protected array|null $data = null;

    /**
     * Attached headers.
     */
    protected array $headers = [];

    /**
     * Construct the exception class.
     */
    public function __construct(string|null $message = null, array|null $headers = null)
    {
        parent::__construct($this->status, $message ?? $this->message, null, $headers ?? $this->headers);
    }

    /**
     * Retrieve the HTTP status code,
     */
    public function statusCode(): int
    {
        return $this->status;
    }

    /**
     * Retrieve the error code.
     */
    public function errorCode(): string|null
    {
        return $this->errorCode;
    }

    /**
     * Retrieve the error message.
     */
    public function message(): string|null
    {
        return $this->message ?: null;
    }

    /**
     * Retrieve additional error data.
     */
    public function data(): array|null
    {
        return $this->data;
    }

    /**
     * Retrieve attached headers.
     */
    public function headers(): array|null
    {
        return $this->headers;
    }
}
