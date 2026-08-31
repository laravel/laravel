<?php

namespace Egulias\EmailValidator\Warning;

class IPV6DoubleColon extends Warning
{
    public const CODE = 73;

    public function __construct()
    {
        $this->message = 'Double colon found after IPV6 tag';
        $this->rfcNumber = 5322;
    }
}
