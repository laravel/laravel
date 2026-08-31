<?php

namespace Egulias\EmailValidator\Result\Reason;

class NoLocalPart implements Reason 
{
    public function code() : int
    {
        return 130;
    }

    public function description() : string
    {
        return "No local part";
    }
}
