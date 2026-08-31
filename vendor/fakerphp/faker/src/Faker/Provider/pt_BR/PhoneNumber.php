<?php

namespace Faker\Provider\pt_BR;

class PhoneNumber extends \Faker\Provider\PhoneNumber
{
    protected static $landlineFormats = ['2###-####', '3###-####', '4###-####'];

    /**
     * Since december 2016 all mobile phone numbers in brazil begin with 9 and landlines 2, 3 or 4.
     *
     * @see http://www.anatel.gov.br/Portal/exibirPortalPaginaEspecial.do?org.apache.struts.taglib.html.TOKEN=9594e1d11fbc996d52bda44e608bb744&codItemCanal=1794&pastaSelecionada=2984
     */
    protected static $cellphoneFormats = ['9####-####'];

    /**
     * Generates a 2-digit area code not composed by zeroes.
     *
     * @see http://www.anatel.gov.br/legislacao/resolucoes/16-2001/383-resolucao-263.
     *
     * @return string
     */
    public static function areaCode()
    {
        $areaCodes = [
            '11', '12', '13', '14', '15', '16', '17', '18', '19', '21', '22', '24',
            '27', '28', '31', '32', '33', '34', '35', '37', '38', '41', '42', '43',
            '44', '45', '46', '47', '48', '49', '51', '53', '54', '55', '61', '62',
            '63', '64', '65', '66', '67', '68', '69', '71', '73', '74', '75', '77',
            '79', '81', '82', '83', '84', '85', '86', '87', '88', '89', '91', '92',
            '93', '94', '95', '96', '97', '98', '99',
        ];

        return self::randomElement($areaCodes);
    }

    /**
     * Generates a 9-digit cellphone number without formatting characters.
     *
     * @param bool $formatted [def: true] If it should return a formatted number or not.
     *
     * @return string
     */
    public static function cellphone($formatted = true)
    {
        $number = static::numerify(static::randomElement(static::$cellphoneFormats));

        if (!$formatted) {
            $number = strtr($number, ['-' => '']);
        }

        return $number;
    }

    /**
     * Generates an 9-digit landline number without formatting characters.
     *
     * @param bool $formatted [def: true] If it should return a formatted number or not.
     *
     * @return string
     */
    public static function landline($formatted = true)
    {
        $number = static::numerify(static::randomElement(static::$landlineFormats));

        if (!$formatted) {
            $number = strtr($number, ['-' => '']);
        }

        return $number;
    }

    /**
     * Randomizes between cellphone and landline numbers.
     *
     * @param bool $formatted [def: true] If it should return a formatted number or not.
     */
    public static function phone($formatted = true)
    {
        $options = static::randomElement([
            ['cellphone', false],
            ['cellphone', true],
            ['landline', null],
        ]);

        return call_user_func([static::class, $options[0]], $formatted, $options[1]);
    }

    /**
     * Generates a complete phone number.
     *
     * @param string $type      [def: landline] One of "landline" or "cellphone". Defaults to "landline" on invalid values.
     * @param bool   $formatted [def: true] If the number should be formatted or not.
     *
     * @return string
     */
    protected static function anyPhoneNumber($type, $formatted = true)
    {
        $area = static::areaCode();
        $number = ($type == 'cellphone') ?
            static::cellphone($formatted) :
            static::landline($formatted);

        return $formatted ? "($area) $number" : $area . $number;
    }

    /**
     * Concatenates {@link areaCode} and {@link cellphone} into a national cellphone number.
     *
     * @param bool $formatted [def: true] If it should return a formatted number or not.
     *
     * @return string
     */
    public static function cellphoneNumber($formatted = true)
    {
        return static::anyPhoneNumber('cellphone', $formatted);
    }

    /**
     * Concatenates {@link areaCode} and {@link landline} into a national landline number.
     *
     * @param bool $formatted [def: true] If it should return a formatted number or not.
     *
     * @return string
     */
    public static function landlineNumber($formatted = true)
    {
        return static::anyPhoneNumber('landline', $formatted);
    }

    /**
     * Randomizes between complete cellphone and landline numbers.
     */
    public function phoneNumber()
    {
        $method = static::randomElement(['cellphoneNumber', 'landlineNumber']);

        return call_user_func([static::class, $method], true);
    }

    /**
     * Randomizes between complete cellphone and landline numbers, cleared from formatting symbols.
     */
    public static function phoneNumberCleared()
    {
        $method = static::randomElement(['cellphoneNumber', 'landlineNumber']);

        return call_user_func([static::class, $method], false);
    }
}
