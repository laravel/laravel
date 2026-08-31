<?php

namespace Egulias\EmailValidator\Result\Reason;

class NoDomainPart implements Reason
{
    public function code() : int
    {
        return 131;
    }

    public function description() : string
    {
        return 'No domain part found';
    }
}
