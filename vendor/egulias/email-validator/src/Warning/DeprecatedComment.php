<?php

namespace Egulias\EmailValidator\Warning;

class DeprecatedComment extends Warning
{
    public const CODE = 37;

    public function __construct()
    {
        $this->message = 'Deprecated comments';
    }
}
