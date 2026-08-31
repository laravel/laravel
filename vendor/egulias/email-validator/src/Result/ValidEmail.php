<?php

namespace Egulias\EmailValidator\Result;

class ValidEmail implements Result
{
    public function isValid(): bool
    {
        return true;
    }

    public function isInvalid(): bool
    {
        return false;
    }

    public function description(): string
    {
        return "Valid email";
    }

    public function code(): int
    {
        return 0;
    }

}
