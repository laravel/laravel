<?php

namespace Mockery\CountValidator;

interface CountValidatorInterface
{
    /**
     * Checks if the validator can accept an additional nth call
     *
     * @param int $n
     *
     * @return bool
     */
    public function isEligible($n);

    /**
     * Validate the call count against this validator
     *
     * @param int $n
     *
     * @return bool
     */
    public function validate($n);
}
