<?php

namespace Egulias\EmailValidator\Warning;

use UnitEnum;

class QuotedPart extends Warning
{
    public const CODE = 36;

    /**
     * @param UnitEnum|string|int|null $prevToken
     * @param UnitEnum|string|int|null $postToken
     */
    public function __construct($prevToken, $postToken)
    {
        if ($prevToken instanceof UnitEnum) {
            $prevToken = $prevToken->name;
        }

        if ($postToken instanceof UnitEnum) {
            $postToken = $postToken->name;
        }

        $this->message = "Deprecated Quoted String found between $prevToken and $postToken";
    }
}
