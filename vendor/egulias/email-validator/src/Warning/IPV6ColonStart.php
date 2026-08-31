<?php

namespace Egulias\EmailValidator\Warning;

class IPV6ColonStart extends Warning
{
    public const CODE = 76;

    public function __construct()
    {
        $this->message = ':: found at the start of the domain literal';
        $this->rfcNumber = 5322;
    }
}
