<?php

namespace Faker\Provider\nl_BE;

class Payment extends \Faker\Provider\Payment
{
    /**
     * International Bank Account Number (IBAN).
     *
     * @see http://en.wikipedia.org/wiki/International_Bank_Account_Number
     *
     * @param string $prefix      for generating bank account number of a specific bank
     * @param string $countryCode ISO 3166-1 alpha-2 country code
     * @param int    $length      total length without country code and 2 check digits
     *
     * @return string
     */
    public static function bankAccountNumber($prefix = '', $countryCode = 'BE', $length = null)
    {
        return static::iban($countryCode, $prefix, $length);
    }

    /**
     * Value Added Tax (VAT).
     *
     * @example 'BE0123456789', ('spaced') 'BE 0123456789'
     *
     * @see http://ec.europa.eu/taxation_customs/vies/faq.html?locale=en#item_11
     * @see http://www.iecomputersystems.com/ordering/eu_vat_numbers.htm
     * @see http://en.wikipedia.org/wiki/VAT_identification_number
     *
     * @param bool $spacedNationalPrefix
     *
     * @return string VAT Number
     */
    public static function vat($spacedNationalPrefix = true)
    {
        $prefix = $spacedNationalPrefix ? 'BE ' : 'BE';

        // Generate 7 numbers of vat.
        $firstSeven = self::randomNumber(7, true);

        // Generate checksum for number
        $checksum = 97 - fmod($firstSeven, 97);

        // '0' + 7 numbers + checksum
        return sprintf('%s0%s%02d', $prefix, $firstSeven, $checksum);
    }
}
