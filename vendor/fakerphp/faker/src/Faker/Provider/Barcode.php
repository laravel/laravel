<?php

namespace Faker\Provider;

use Faker\Calculator\Ean;
use Faker\Calculator\Isbn;

/**
 * @see http://en.wikipedia.org/wiki/EAN-13
 * @see http://en.wikipedia.org/wiki/ISBN
 */
class Barcode extends Base
{
    private function ean($length = 13)
    {
        $code = static::numerify(str_repeat('#', $length - 1));

        return $code . Ean::checksum($code);
    }

    /**
     * Utility function for computing EAN checksums
     *
     * @deprecated Use \Faker\Calculator\Ean::checksum() instead
     *
     * @param string $input
     *
     * @return int
     */
    protected static function eanChecksum($input)
    {
        return Ean::checksum($input);
    }

    /**
     * ISBN-10 check digit
     *
     * @see http://en.wikipedia.org/wiki/International_Standard_Book_Number#ISBN-10_check_digits
     * @deprecated Use \Faker\Calculator\Isbn::checksum() instead
     *
     * @param string $input ISBN without check-digit
     *
     * @throws \LengthException When wrong input length passed
     *
     * @return string
     */
    protected static function isbnChecksum($input)
    {
        return Isbn::checksum($input);
    }

    /**
     * Get a random EAN13 barcode.
     *
     * @return string
     *
     * @example '4006381333931'
     */
    public function ean13()
    {
        return $this->ean(13);
    }

    /**
     * Get a random EAN8 barcode.
     *
     * @return string
     *
     * @example '73513537'
     */
    public function ean8()
    {
        return $this->ean(8);
    }

    /**
     * Get a random ISBN-10 code
     *
     * @see http://en.wikipedia.org/wiki/International_Standard_Book_Number
     *
     * @return string
     *
     * @example '4881416324'
     */
    public function isbn10()
    {
        $code = static::numerify(str_repeat('#', 9));

        return $code . Isbn::checksum($code);
    }

    /**
     * Get a random ISBN-13 code
     *
     * @see http://en.wikipedia.org/wiki/International_Standard_Book_Number
     *
     * @return string
     *
     * @example '9790404436093'
     */
    public function isbn13()
    {
        $code = '97' . self::numberBetween(8, 9) . static::numerify(str_repeat('#', 9));

        return $code . Ean::checksum($code);
    }
}
