<?php

namespace App\Exceptions;

use ProjectName\Exceptions\DomainException;

class DomainExceptionDecorator extends \DomainException
{
    public function __construct(DomainException $exception)
    {
        parent::__construct(trans($exception->getKey()), $exception->getCode(), $exception->getPrevious());
    }
}
