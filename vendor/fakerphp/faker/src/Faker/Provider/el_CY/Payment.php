<?php

namespace Faker\Provider\el_CY;

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
    public static function bankAccountNumber($prefix = '', $countryCode = 'CY', $length = null)
    {
        return static::iban($countryCode, $prefix, $length);
    }

    /**
     * @var array Cyprus banks
     *
     * @see http://www.acb.com.cy/cgibin/hweb?-A=206&-V=membership
     */
    protected static $banks = [
        'Τράπεζα Κύπρου',
        'Ελληνική Τράπεζα',
        'Alpha Bank Cyprus',
        'Εθνική Τράπεζα της Ελλάδος (Κύπρου)',
        'USB BANK',
        'Κυπριακή Τράπεζα Αναπτύξεως',
        'Societe Gererale Cyprus',
        'Τράπεζα Πειραιώς (Κύπρου)',
        'RCB Bank',
        'Eurobank Cyprus',
        'Συνεργατική Κεντρική Τράπεζα',
        'Ancoria Bank',
    ];

    /**
     * @example 'Τράπεζα Κύπρου'
     */
    public static function bank()
    {
        return static::randomElement(static::$banks);
    }
}
