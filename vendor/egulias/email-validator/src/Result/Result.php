<?php

namespace Egulias\EmailValidator\Result;

interface Result
{
    /**
     * Is validation result valid?
     * 
     */
    public function isValid(): bool;

    /**
     * Is validation result invalid?
     * Usually the inverse of isValid()
     * 
     */
    public function isInvalid(): bool;

    /**
     * Short description of the result, human readable.
     * 
     */
    public function description(): string;

    /**
     * Code for user land to act upon.
     * 
     */
    public function code(): int;
}
