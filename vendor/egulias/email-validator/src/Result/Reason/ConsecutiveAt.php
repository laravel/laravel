<?php

namespace Egulias\EmailValidator\Result\Reason;

class ConsecutiveAt implements Reason
{
    public function code() : int
    {
        return 128;
    }

    public function description() : string
    {
        return '@ found after another @';
    }

}
