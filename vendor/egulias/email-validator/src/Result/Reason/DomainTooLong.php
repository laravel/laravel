<?php

namespace Egulias\EmailValidator\Result\Reason;

class DomainTooLong implements Reason
{
    public function code() : int
    {
        return 244;
    }

    public function description() : string
    {
        return 'Domain is longer than 253 characters';
    }
}
