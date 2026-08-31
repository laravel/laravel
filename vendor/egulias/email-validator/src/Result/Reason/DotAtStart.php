<?php

namespace Egulias\EmailValidator\Result\Reason;

class DotAtStart implements Reason
{
    public function code() : int
    {
        return 141;
    }

    public function description() : string
    {
        return "Starts with a DOT";
    }
}
