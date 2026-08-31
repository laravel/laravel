<?php

namespace Faker\Provider;

class Company extends Base
{
    protected static $formats = [
        '{{lastName}} {{companySuffix}}',
    ];

    protected static $companySuffix = ['Ltd'];

    protected static $jobTitleFormat = [
        '{{word}}',
    ];

    /**
     * @example 'Acme Ltd'
     *
     * @return string
     */
    public function company()
    {
        $format = static::randomElement(static::$formats);

        return $this->generator->parse($format);
    }

    /**
     * @example 'Ltd'
     *
     * @return string
     */
    public static function companySuffix()
    {
        return static::randomElement(static::$companySuffix);
    }

    /**
     * @example 'Job'
     *
     * @return string
     */
    public function jobTitle()
    {
        $format = static::randomElement(static::$jobTitleFormat);

        return $this->generator->parse($format);
    }
}
