<?php

namespace Faker\Provider\vi_VN;

class PhoneNumber extends \Faker\Provider\PhoneNumber
{
    protected static $areaCodes = [
        76, 281, 64, 781, 240, 241,
        75, 650, 56, 651, 62, 780,
        26, 710, 511, 500, 510, 230,
        61, 67, 59, 219, 351, 4,
        39, 320, 31, 711, 218, 321,
        8, 58, 77, 60, 231, 25,
        20, 63, 72, 350, 38, 30,
        68, 210, 57, 52, 510, 55,
        33, 53, 79, 22, 66, 36,
        280, 37, 54, 73, 74, 27,
        70, 211, 29,
        // Mobile
        96, 97, 98, 162, 163, 164, 165, 166, 167, 168, 169, // Viettel
        91, 94, 123, 124, 125, 127, 129, // Vinaphone
        90, 93, 120, 121, 122, 126, 128, // Mobifone
        92, 186, 188, // Vietnamobile
        99, 199, // Gmobile
        95, // Sfone
    ];

    protected static $formats = [
        '7' => [
            '0[a] ### ####',
            '(0[a]) ### ####',
            '0[a]-###-####',
            '(0[a])###-####',
            '84-[a]-###-####',
            '(84)([a])###-####',
            '+84-[a]-###-####',
        ],
        '8' => [
            '0[a] #### ####',
            '(0[a]) #### ####',
            '0[a]-####-####',
            '(0[a])####-####',
            '84-[a]-####-####',
            '(84)([a])####-####',
            '+84-[a]-####-####',
        ],
    ];

    public function phoneNumber()
    {
        $areaCode = static::randomElement(static::$areaCodes);
        $areaCodeLength = strlen($areaCode);
        $digits = 7;

        if ($areaCodeLength < 2) {
            $digits = 8;
        }

        return static::numerify(str_replace('[a]', $areaCode, static::randomElement(static::$formats[$digits])));
    }
}
