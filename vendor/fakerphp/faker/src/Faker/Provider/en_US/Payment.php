<?php

namespace Faker\Provider\en_US;

class Payment extends \Faker\Provider\Payment
{
    public function bankAccountNumber()
    {
        // Length between 5 and 17, biased towards center
        $length = self::numberBetween(0, 3) + self::numberBetween(0, 3) + self::numberBetween(0, 3) + self::numberBetween(0, 3) + 5;

        return self::numerify(str_repeat('#', $length));
    }

    public function bankRoutingNumber()
    {
        $district = self::numberBetween(1, 12);
        $type = self::randomElement([0, 0, 0, 0, 20, 20, 60]);
        $clearingCenter = self::randomDigitNotNull();
        $state = self::randomDigit();
        $institution = self::randomNumber(4, true);

        $result = sprintf('%02d%01d%01d%04d', $district + $type, $clearingCenter, $state, $institution);

        return $result . self::calculateRoutingNumberChecksum($result);
    }

    public static function calculateRoutingNumberChecksum($routing)
    {
        return (
            7 * ($routing[0] + $routing[3] + $routing[6]) +
            3 * ($routing[1] + $routing[4] + $routing[7]) +
            9 * ($routing[2] + $routing[5])
        ) % 10;
    }
}
