<?php

namespace Faker\Provider\fr_FR;

class Payment extends \Faker\Provider\Payment
{
    /**
     * Value Added Tax (VAT)
     *
     * @example 'FR12123456789', ('spaced') 'FR 12 123 456 789'
     *
     * @see http://ec.europa.eu/taxation_customs/vies/faq.html?locale=en#item_11
     * @see http://www.iecomputersystems.com/ordering/eu_vat_numbers.htm
     * @see http://en.wikipedia.org/wiki/VAT_identification_number
     *
     * @param bool $spacedNationalPrefix
     *
     * @return string VAT Number
     */
    public function vat($spacedNationalPrefix = true)
    {
        $siren = Company::siren(false);
        $key = (12 + 3 * ($siren % 97)) % 97;
        $pattern = "%s%'.02d%s";

        if ($spacedNationalPrefix) {
            $siren = trim(chunk_split($siren, 3, ' '));
            $pattern = "%s %'.02d %s";
        }

        return sprintf($pattern, 'FR', $key, $siren);
    }

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
    public static function bankAccountNumber($prefix = '', $countryCode = 'FR', $length = null)
    {
        return static::iban($countryCode, $prefix, $length);
    }
}
