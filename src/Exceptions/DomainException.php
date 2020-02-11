<?php

namespace ProjectName\Exceptions;

use Throwable;

abstract class DomainException extends \DomainException
{
    /** @var string */
    private $key;

    public function __construct(string $key, string $message = '', int $code = 0, Throwable $previous = null)
    {
        $this->key = $key;
        parent::__construct($message, $code, $previous);
    }

    public function getKey(): string
    {
        return $this->key;
    }
}
