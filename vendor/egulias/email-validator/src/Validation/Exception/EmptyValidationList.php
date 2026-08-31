<?php

namespace Egulias\EmailValidator\Validation\Exception;

use Exception;

class EmptyValidationList extends \InvalidArgumentException
{
    /**
    * @param int $code
    */
    public function __construct($code = 0, ?Exception $previous = null)
    {
        parent::__construct("Empty validation list is not allowed", $code, $previous);
    }
}
