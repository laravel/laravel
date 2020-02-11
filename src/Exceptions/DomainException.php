<?php

namespace ProjectName\Exceptions;

use Throwable;

abstract class DomainException extends \RuntimeException
{
    /** @var string */
    private $key;

    public function __construct(string $key, string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->key = $key;
    }

    public function getKey(): string
    {
        return $this->key;
    }
}
