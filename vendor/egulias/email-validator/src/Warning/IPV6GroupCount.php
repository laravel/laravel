<?php

namespace Egulias\EmailValidator\Warning;

class IPV6GroupCount extends Warning
{
    public const CODE = 72;

    public function __construct()
    {
        $this->message = 'Group count is not IPV6 valid';
        $this->rfcNumber = 5322;
    }
}
