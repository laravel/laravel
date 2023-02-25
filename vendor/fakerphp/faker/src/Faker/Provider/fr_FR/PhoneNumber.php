<?php

namespace Faker\Provider\fr_FR;

class PhoneNumber extends \Faker\Provider\PhoneNumber
{
    // Phone numbers can't start by 00 in France
    // 01 is the most common prefix
    protected static $formats = [
        '+33 (0)1 ## ## ## ##',
        '+33 (0)1 ## ## ## ##',
        '+33 (0)2 ## ## ## ##',
        '+33 (0)3 ## ## ## ##',
        '+33 (0)4 ## ## ## ##',
        '+33 (0)5 ## ## ## ##',
        '+33 (0)6 ## ## ## ##',
        '+33 (0)7 {{phoneNumber07WithSeparator}}',
        '+33 (0)8 {{phoneNumber08WithSeparator}}',
        '+33 (0)9 ## ## ## ##',
        '+33 1 ## ## ## ##',
        '+33 1 ## ## ## ##',
        '+33 2 ## ## ## ##',
        '+33 3 ## ## ## ##',
        '+33 4 ## ## ## ##',
        '+33 5 ## ## ## ##',
        '+33 6 ## ## ## ##',
        '+33 7 {{phoneNumber07WithSeparator}}',
        '+33 8 {{phoneNumber08WithSeparator}}',
        '+33 9 ## ## ## ##',
        '01########',
        '01########',
        '02########',
        '03########',
        '04########',
        '05########',
        '06########',
        '07{{phoneNumber07}}',
        '08{{phoneNumber08}}',
        '09########',
        '01 ## ## ## ##',
        '01 ## ## ## ##',
        '02 ## ## ## ##',
        '03 ## ## ## ##',
        '04 ## ## ## ##',
        '05 ## ## ## ##',
        '06 ## ## ## ##',
        '07 {{phoneNumber07WithSeparator}}',
        '08 {{phoneNumber08WithSeparator}}',
        '09 ## ## ## ##',
    ];

    // Mobile phone numbers start by 06 and 07
    // 06 is the most common prefix
    protected static $mobileFormats = [
        '+33 (0)6 ## ## ## ##',
        '+33 6 ## ## ## ##',
        '+33 (0)7 {{phoneNumber07WithSeparator}}',
        '+33 7 {{phoneNumber07WithSeparator}}',
        '06########',
        '07{{phoneNumber07}}',
        '06 ## ## ## ##',
        '07 {{phoneNumber07WithSeparator}}',
    ];

    protected static $serviceFormats = [
        '+33 (0)8 {{phoneNumber08WithSeparator}}',
        '+33 8 {{phoneNumber08WithSeparator}}',
        '08 {{phoneNumber08WithSeparator}}',
        '08{{phoneNumber08}}',
    ];

    protected static $e164Formats = [
        '+33##########',
    ];

    public function phoneNumber07()
    {
        $phoneNumber = $this->phoneNumber07WithSeparator();

        return str_replace(' ', '', $phoneNumber);
    }

    /**
     * Only 073 to 079 are acceptable prefixes with 07
     *
     * @see http://www.arcep.fr/index.php?id=8146
     */
    public function phoneNumber07WithSeparator()
    {
        $phoneNumber = $this->generator->numberBetween(3, 9);
        $phoneNumber .= $this->numerify('# ## ## ##');

        return $phoneNumber;
    }

    public function phoneNumber08()
    {
        $phoneNumber = $this->phoneNumber08WithSeparator();

        return str_replace(' ', '', $phoneNumber);
    }

    /**
     *  Valid formats for 08:
     *
     *  0# ## ## ##
     *  1# ## ## ##
     *  2# ## ## ##
     *  91 ## ## ##
     *  92 ## ## ##
     *  93 ## ## ##
     *  97 ## ## ##
     *  98 ## ## ##
     *  99 ## ## ##
     *
     *  Formats 089(4|6)## ## ## are valid, but will be
     *  attributed when other 089 resource ranges are exhausted.
     *
     * @see https://www.arcep.fr/index.php?id=8146#c9625
     * @see https://issuetracker.google.com/u/1/issues/73269839
     */
    public function phoneNumber08WithSeparator()
    {
        $regex = '([012]{1}\d{1}|(9[1-357-9])( \d{2}){3}';

        return $this->regexify($regex);
    }

    /**
     * @example '0601020304'
     */
    public function mobileNumber()
    {
        $format = static::randomElement(static::$mobileFormats);

        return static::numerify($this->generator->parse($format));
    }

    /**
     * @example '0891951357'
     */
    public function serviceNumber()
    {
        $format = static::randomElement(static::$serviceFormats);

        return static::numerify($this->generator->parse($format));
    }
}
