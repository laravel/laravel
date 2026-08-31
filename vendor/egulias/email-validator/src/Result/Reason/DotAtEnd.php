<?php

namespace Egulias\EmailValidator\Result\Reason;

class DotAtEnd implements Reason
{
    public function code() : int
    {
        return 142;
    }

    public function description() : string
    {
        return 'Dot at the end';
    }
}
