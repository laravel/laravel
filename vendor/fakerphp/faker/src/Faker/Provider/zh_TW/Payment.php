<?php

namespace Faker\Provider\zh_TW;

/**
 * @deprecated Use {@link \Faker\Provider\Payment} instead
 * @see \Faker\Provider\Payment
 */
class Payment extends \Faker\Provider\Payment
{
    /**
     * @return array
     *
     * @deprecated Use {@link \Faker\Provider\Payment::creditCardDetails()} instead
     * @see \Faker\Provider\Payment::creditCardDetails()
     */
    public function creditCardDetails($valid = true)
    {
        return parent::creditCardDetails($valid);
    }
}
