<?php

namespace Faker\Provider\fi_FI;

class PhoneNumber extends \Faker\Provider\PhoneNumber
{
    /**
     * @see https://www.viestintavirasto.fi/en/internettelephone/numberingoftelecommunicationsnetworks/localcallsandtelecommunicationsareas/mapoftelecommunicationsareas.html
     *
     * @var array
     */
    protected static $landLineareaCodes = [
        '02',
        '03',
        '05',
        '06',
        '08',
        '09',
        '013',
        '014',
        '015',
        '016',
        '017',
        '018',
        '019',
    ];

    /**
     * @see https://www.viestintavirasto.fi/en/internettelephone/numberingoftelecommunicationsnetworks/mobilenetworks/mobilenetworkareacodes.html
     *
     * @var array
     */
    protected static $mobileNetworkAreaCodes = [
        '040',
        '050',
        '044',
        '045',
    ];

    protected static $numberFormats = [
        '### ####',
        '#######',
    ];

    protected static $formats = [
        '+358 ({{ e164MobileNetworkAreaCode }}) {{ numberFormat }}',
        '+358 {{ e164MobileNetworkAreaCode }} {{ numberFormat }}',
        '+358 ({{ e164landLineAreaCode }}) {{ numberFormat }}',
        '+358 {{ e164landLineAreaCode }} {{ numberFormat }}',
        '{{ mobileNetworkAreaCode }}{{ separator }}{{ numberFormat }}',
        '{{ landLineAreaCode }}{{ separator }}{{ numberFormat }}',
    ];

    /**
     * @return string
     */
    public function landLineAreaCode()
    {
        return static::randomElement(static::$landLineareaCodes);
    }

    /**
     * @return string
     */
    public function e164landLineAreaCode()
    {
        return substr(static::randomElement(static::$landLineareaCodes), 1);
    }

    /**
     * @return string
     */
    public function mobileNetworkAreaCode()
    {
        return static::randomElement(static::$mobileNetworkAreaCodes);
    }

    /**
     * @return string
     */
    public function e164MobileNetworkAreaCode()
    {
        return substr(static::randomElement(static::$mobileNetworkAreaCodes), 1);
    }

    /**
     * @return string
     */
    public function numberFormat()
    {
        return static::randomElement(static::$numberFormats);
    }

    /**
     * @return string
     */
    public function separator()
    {
        return static::randomElement([' ', '-']);
    }
}
