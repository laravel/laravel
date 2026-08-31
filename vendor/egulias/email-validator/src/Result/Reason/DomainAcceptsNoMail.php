<?php

namespace Egulias\EmailValidator\Result\Reason;

class DomainAcceptsNoMail implements Reason
{
    public function code() : int
    {
        return 154;
    }

    public function description() : string
    {
        return 'Domain accepts no mail (Null MX, RFC7505)';
    }
}
