<?php

namespace Egulias\EmailValidator\Warning;

class DomainLiteral extends Warning
{
    public const CODE = 70;

    public function __construct()
    {
        $this->message = 'Domain Literal';
        $this->rfcNumber = 5322;
    }
}
