<?php

namespace Egulias\EmailValidator\Result\Reason;

class AtextAfterCFWS implements Reason
{
    public function code() : int
    {
        return 133;
    }

    public function description() : string
    {
        return 'ATEXT found after CFWS';
    }
}
