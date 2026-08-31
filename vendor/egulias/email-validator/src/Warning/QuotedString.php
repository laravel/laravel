<?php

namespace Egulias\EmailValidator\Warning;

class QuotedString extends Warning
{
    public const CODE = 11;

    /**
     * @param string|int $prevToken
     * @param string|int $postToken
     */
    public function __construct($prevToken, $postToken)
    {
        $this->message = "Quoted String found between $prevToken and $postToken";
    }
}
