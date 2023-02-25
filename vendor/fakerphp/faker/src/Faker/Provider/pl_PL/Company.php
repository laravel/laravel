<?php

namespace Faker\Provider\pl_PL;

class Company extends \Faker\Provider\Company
{
    protected static $formats = [
        '{{lastName}}',
        '{{lastName}}',
        '{{lastName}} {{companySuffix}}',
        '{{lastName}} {{companySuffix}}',
        '{{lastName}} {{companySuffix}}',
        '{{lastName}} {{companySuffix}}',
        '{{companyPrefix}} {{lastName}}',
        '{{lastName}}-{{lastName}}',
    ];

    protected static $companySuffix = ['S.A.', 'i syn', 'sp. z o.o.', 'sp. j.', 'sp. p.', 'sp. k.', 'S.K.A', 's. c.', 'P.P.O.F'];

    protected static $companyPrefix = ['Grupa', 'Fundacja', 'Stowarzyszenie', 'Spółdzielnia'];

    /**
     * @example 'Grupa'
     */
    public static function companyPrefix()
    {
        return static::randomElement(static::$companyPrefix);
    }

    /**
     * Register of the National Economy
     *
     * @see http://pl.wikipedia.org/wiki/REGON
     *
     * @return string 9 digit number
     */
    public static function regon()
    {
        $weights = [8, 9, 2, 3, 4, 5, 6, 7];
        $regionNumber = self::numberBetween(0, 49) * 2 + 1;
        $result = [(int) ($regionNumber / 10), $regionNumber % 10];

        for ($i = 2, $size = count($weights); $i < $size; ++$i) {
            $result[$i] = static::randomDigit();
        }
        $checksum = 0;

        for ($i = 0, $size = count($result); $i < $size; ++$i) {
            $checksum += $weights[$i] * $result[$i];
        }
        $checksum %= 11;

        if ($checksum == 10) {
            $checksum = 0;
        }
        $result[] = $checksum;

        return implode('', $result);
    }

    /**
     * Register of the National Economy, local entity number
     *
     * @see http://pl.wikipedia.org/wiki/REGON
     *
     * @return 14 digit number
     */
    public static function regonLocal()
    {
        $weights = [2, 4, 8, 5, 0, 9, 7, 3, 6, 1, 2, 4, 8];
        $result = str_split(static::regon());

        for ($i = count($result), $size = count($weights); $i < $size; ++$i) {
            $result[$i] = static::randomDigit();
        }
        $checksum = 0;

        for ($i = 0, $size = count($result); $i < $size; ++$i) {
            $checksum += $weights[$i] * $result[$i];
        }
        $checksum %= 11;

        if ($checksum == 10) {
            $checksum = 0;
        }
        $result[] = $checksum;

        return implode('', $result);
    }
}
