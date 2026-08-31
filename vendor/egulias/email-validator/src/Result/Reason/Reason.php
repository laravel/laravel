<?php

namespace Egulias\EmailValidator\Result\Reason;

interface Reason
{
    /**
     * Code for user land to act upon;
     */
    public function code() : int;

    /**
     * Short description of the result, human readable.
     */
    public function description() : string;
}
