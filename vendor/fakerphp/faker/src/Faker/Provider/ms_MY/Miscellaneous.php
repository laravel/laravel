<?php

namespace Faker\Provider\ms_MY;

class Miscellaneous extends \Faker\Provider\Miscellaneous
{
    /**
     * @see https://en.wikipedia.org/wiki/Vehicle_registration_plates_of_Malaysia
     */
    protected static $jpjNumberPlateFormats = [
        '{{peninsularPrefix}}{{validAlphabet}}{{validAlphabet}} {{numberSequence}}',
        '{{peninsularPrefix}}{{validAlphabet}}{{validAlphabet}} {{numberSequence}}',
        '{{peninsularPrefix}}{{validAlphabet}}{{validAlphabet}} {{numberSequence}}',
        '{{peninsularPrefix}}{{validAlphabet}}{{validAlphabet}} {{numberSequence}}',
        'W{{validAlphabet}}{{validAlphabet}} {{numberSequence}} {{validAlphabet}}',
        'KV {{numberSequence}} {{validAlphabet}}',
        '{{sarawakPrefix}} {{numberSequence}} {{validAlphabet}}',
        '{{sabahPrefix}} {{numberSequence}} {{validAlphabet}}',
        '{{specialPrefix}} {{numberSequence}}',
    ];

    /**
     * Some alphabet has higher frequency that coincides with the current number
     * of registrations. E.g. W = Wilayah Persekutuan
     *
     * @see https://en.wikipedia.org/wiki/Vehicle_registration_plates_of_Malaysia#Current_format
     */
    protected static $peninsularPrefix = [
        'A', 'A', 'B', 'C', 'D', 'F', 'J', 'J', 'K', 'M', 'N', 'P', 'P', 'R', 'T', 'V',
        'W', 'W', 'W', 'W', 'W', 'W',
    ];

    /**
     * @see https://en.wikipedia.org/wiki/Vehicle_registration_plates_of_Malaysia#Current_format_2
     */
    protected static $sarawakPrefix = [
        'QA', 'QK', 'QB', 'QC', 'QL', 'QM', 'QP', 'QR', 'QS', 'QT',
    ];

    /**
     * @see https://en.wikipedia.org/wiki/Vehicle_registration_plates_of_Malaysia#Current_format_3
     */
    protected static $sabahPrefix = [
        'SA', 'SAA', 'SAB', 'SAC', 'SB', 'SD', 'SG',
        'SK', 'SL', 'SS', 'SSA', 'ST', 'STA', 'SU',
    ];

    /**
     * @see https://en.wikipedia.org/wiki/Vehicle_registration_plates_of_Malaysia#Commemorative_plates
     */
    protected static $specialPrefix = [
        '1M4U',
        'A1M',
        'BAMbee',
        'Chancellor',
        'G', 'G1M', 'GP', 'GT',
        'Jaguh',
        'K1M', 'KRISS',
        'LOTUS',
        'NAAM', 'NAZA', 'NBOS',
        'PATRIOT', 'Perdana', 'PERFECT', 'Perodua', 'Persona', 'Proton', 'Putra', 'PUTRAJAYA',
        'RIMAU',
        'SAM', 'SAS', 'Satria', 'SMS', 'SUKOM',
        'T1M', 'Tiara', 'TTB',
        'U', 'US',
        'VIP',
        'WAJA',
        'XIIINAM', 'XOIC', 'XXVIASEAN', 'XXXIDB',
        'Y',
    ];

    /**
     * Chances of having an empty alphabet will be 1/24
     *
     * @see https://en.wikipedia.org/wiki/Vehicle_registration_plates_of_Malaysia#Current_format
     */
    protected static $validAlphabets = [
        'A', 'B', 'C', 'D', 'E', 'F',
        'G', 'H', 'J', 'K', 'L', 'M',
        'N', 'P', 'Q', 'R', 'S', 'T',
        'U', 'V', 'W', 'X', 'Y', '',
    ];

    /**
     * Return a valid Malaysia JPJ(Road Transport Department) vehicle licence plate number
     *
     * @example 'WKN 2368'
     *
     * @return string
     */
    public function jpjNumberPlate()
    {
        $formats = static::toUpper(static::lexify(static::bothify(static::randomElement(static::$jpjNumberPlateFormats))));

        return $this->generator->parse($formats);
    }

    /**
     * Return Peninsular prefix alphabet
     *
     * @example 'W'
     *
     * @return string
     */
    public static function peninsularPrefix()
    {
        return static::randomElement(static::$peninsularPrefix);
    }

    /**
     * Return Sarawak state prefix alphabet
     *
     * @example 'QA'
     *
     * @return string
     */
    public static function sarawakPrefix()
    {
        return static::randomElement(static::$sarawakPrefix);
    }

    /**
     * Return Sabah state prefix alphabet
     *
     * @example 'SA'
     *
     * @return string
     */
    public static function sabahPrefix()
    {
        return static::randomElement(static::$sabahPrefix);
    }

    /**
     * Return specialty licence plate prefix
     *
     * @example 'G1M'
     *
     * @return string
     */
    public static function specialPrefix()
    {
        return static::randomElement(static::$specialPrefix);
    }

    /**
     * Return a valid license plate alphabet
     *
     * @example 'A'
     *
     * @return string
     */
    public static function validAlphabet()
    {
        return static::randomElement(static::$validAlphabets);
    }

    /**
     * Return a valid number sequence between 1 and 9999
     *
     * @example '1234'
     *
     * @return int
     */
    public static function numberSequence()
    {
        return self::numberBetween(1, 9999);
    }
}
