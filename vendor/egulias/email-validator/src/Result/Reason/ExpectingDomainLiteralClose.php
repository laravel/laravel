<?php

namespace Egulias\EmailValidator\Result\Reason;

class ExpectingDomainLiteralClose implements Reason
{
    public function code() : int
    {
        return 137;
    }

    public function description() : string
    {
        return "Closing bracket ']' for domain literal not found";
    }
}
