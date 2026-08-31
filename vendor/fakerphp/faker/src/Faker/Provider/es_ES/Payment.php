<?php

namespace Faker\Provider\es_ES;

class Payment extends \Faker\Provider\Payment
{
    private static $vatMap = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'N', 'P', 'Q', 'R', 'S', 'U', 'V', 'W'];

    /**
     * International Bank Account Number (IBAN)
     *
     * @see http://en.wikipedia.org/wiki/International_Bank_Account_Number
     *
     * @param string $prefix      for generating bank account number of a specific bank
     * @param string $countryCode ISO 3166-1 alpha-2 country code
     * @param int    $length      total length without country code and 2 check digits
     *
     * @return string
     */
    public static function bankAccountNumber($prefix = '', $countryCode = 'ES', $length = null)
    {
        return static::iban($countryCode, $prefix, $length);
    }

    /**
     * Value Added Tax (VAT)
     *
     * @example 'B93694545'
     *
     * @see https://en.wikipedia.org/wiki/VAT_identification_number
     * @see https://es.wikipedia.org/wiki/C%C3%B3digo_de_identificaci%C3%B3n_fiscal
     *
     * @return string VAT Number
     */
    public static function vat()
    {
        $letter = static::randomElement(self::$vatMap);
        $number = static::numerify('########');

        return $letter . $number;
    }
}
