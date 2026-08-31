<?php

namespace Faker\Provider\sv_SE;

/**
 * @see https://www.pts.se/sv/bransch/telefoni/nummer-och-adressering/telefoninummerplanen/telefonnummers-struktur/
 */
class PhoneNumber extends \Faker\Provider\PhoneNumber
{
    /**
     * @var array Swedish phone number formats
     */
    protected static $formats = [
        '08-### ### ##',
        '0%#-### ## ##',
        '0%########',
        '+46 (0)%## ### ###',
        '+46(0)%########',
        '+46 %## ### ###',
        '+46%########',

        '08-### ## ##',
        '0%#-## ## ##',
        '0%##-### ##',
        '0%#######',
        '+46 (0)8 ### ## ##',
        '+46 (0)%# ## ## ##',
        '+46 (0)%## ### ##',
        '+46 (0)%#######',
        '+46(0)%#######',
        '+46%#######',

        '08-## ## ##',
        '0%#-### ###',
        '0%#######',
        '+46 (0)%######',
        '+46(0)%######',
        '+46%######',
    ];

    /**
     * @var array<int, string> Swedish mobile number formats
     */
    protected static array $mobileFormats = [
        '+467########',
        '+46(0)7########',
        '+46 (0)7## ## ## ##',
        '+46 (0)7## ### ###',
        '07## ## ## ##',
        '07## ### ###',
        '07##-## ## ##',
        '07##-### ###',
        '07# ### ## ##',
        '07#-### ## ##',
        '07#-#######',
    ];

    public function mobileNumber(): string
    {
        $format = static::randomElement(static::$mobileFormats);

        return self::numerify($this->generator->parse($format));
    }
}
