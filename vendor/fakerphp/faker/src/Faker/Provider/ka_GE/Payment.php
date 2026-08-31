<?php

namespace Faker\Provider\ka_GE;

class Payment extends \Faker\Provider\Payment
{
    /**
     * @see list of Georgian banks (2015-12-26), source: https://www.nbg.gov.ge/index.php?m=403
     */
    protected static $banks = [
        'ბანკი რესპუბლიკა',
        'თიბისი ბანკი',
        'საქართველოს ბანკი',
        'ლიბერთი ბანკი',
        'ბაზისბანკი',
        'ვითიბი ბანკი ჯორჯია',
        'ბანკი ქართუ',
        'პროკრედიტ ბანკი',
        'სილქ როუდ ბანკი ',
        'კაპიტალ ბანკი ',
        'აზერბაიჯანის საერთაშორისო ბანკი - საქართველო ',
        'ზირაათ ბანკის თბილისის ფილიალი ',
        'კავკასიის განვითარების ბანკი - საქართველო',
        'იშ ბანკი საქართველო',
        'პროგრეს ბანკი',
        'კორ სტანდარტ ბანკი',
        'ხალიკ ბანკი საქართველო ',
        'პაშა ბანკი საქართველო',
        'ფინკა ბანკი საქართველო',
    ];

    /**
     * @example 'თიბისი ბანკი'
     */
    public static function bank()
    {
        return static::randomElement(static::$banks);
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
    public static function bankAccountNumber($prefix = '', $countryCode = 'GE', $length = null)
    {
        return static::iban($countryCode, $prefix, $length);
    }
}
