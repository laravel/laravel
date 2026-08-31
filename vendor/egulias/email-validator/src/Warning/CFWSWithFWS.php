<?php

namespace Egulias\EmailValidator\Warning;

class CFWSWithFWS extends Warning
{
    public const CODE = 18;

    public function __construct()
    {
        $this->message = 'Folding whites space followed by folding white space';
    }
}
