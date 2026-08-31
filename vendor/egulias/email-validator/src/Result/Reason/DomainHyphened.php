<?php

namespace Egulias\EmailValidator\Result\Reason;

class DomainHyphened extends DetailedReason
{
    public function code() : int
    {
        return 144;
    }

    public function description() : string
    {
        return 'S_HYPHEN found in domain';
    }
}
