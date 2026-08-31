<?php

namespace Egulias\EmailValidator\Result\Reason;

class ConsecutiveDot implements Reason
{
    public function code() : int
    {
        return 132;
    }

    public function description() : string
    {
        return 'Concecutive DOT found';
    }
}
