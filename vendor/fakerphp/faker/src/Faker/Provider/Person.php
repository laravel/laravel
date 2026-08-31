<?php

namespace Faker\Provider;

class Person extends Base
{
    public const GENDER_MALE = 'male';
    public const GENDER_FEMALE = 'female';

    protected static $titleFormat = [
        '{{titleMale}}',
        '{{titleFemale}}',
    ];

    protected static $firstNameFormat = [
        '{{firstNameMale}}',
        '{{firstNameFemale}}',
    ];

    protected static $maleNameFormats = [
        '{{firstNameMale}} {{lastName}}',
    ];

    protected static $femaleNameFormats = [
        '{{firstNameFemale}} {{lastName}}',
    ];

    protected static $firstNameMale = [
        'John',
    ];

    protected static $firstNameFemale = [
        'Jane',
    ];

    protected static $lastName = ['Doe'];

    protected static $titleMale = ['Mr.', 'Dr.', 'Prof.'];

    protected static $titleFemale = ['Mrs.', 'Ms.', 'Miss', 'Dr.', 'Prof.'];

    /**
     * @param string|null $gender 'male', 'female' or null for any
     *
     * @return string
     *
     * @example 'John Doe'
     */
    public function name($gender = null)
    {
        if ($gender === static::GENDER_MALE) {
            $format = static::randomElement(static::$maleNameFormats);
        } elseif ($gender === static::GENDER_FEMALE) {
            $format = static::randomElement(static::$femaleNameFormats);
        } else {
            $format = static::randomElement(array_merge(static::$maleNameFormats, static::$femaleNameFormats));
        }

        return $this->generator->parse($format);
    }

    /**
     * @param string|null $gender 'male', 'female' or null for any
     *
     * @return string
     *
     * @example 'John'
     */
    public function firstName($gender = null)
    {
        if ($gender === static::GENDER_MALE) {
            return static::firstNameMale();
        }

        if ($gender === static::GENDER_FEMALE) {
            return static::firstNameFemale();
        }

        return $this->generator->parse(static::randomElement(static::$firstNameFormat));
    }

    /**
     * @return string
     */
    public static function firstNameMale()
    {
        return static::randomElement(static::$firstNameMale);
    }

    /**
     * @return string
     */
    public static function firstNameFemale()
    {
        return static::randomElement(static::$firstNameFemale);
    }

    /**
     * @example 'Doe'
     *
     * @return string
     */
    public function lastName()
    {
        return static::randomElement(static::$lastName);
    }

    /**
     * @example 'Mrs.'
     *
     * @param string|null $gender 'male', 'female' or null for any
     *
     * @return string
     */
    public function title($gender = null)
    {
        if ($gender === static::GENDER_MALE) {
            return static::titleMale();
        }

        if ($gender === static::GENDER_FEMALE) {
            return static::titleFemale();
        }

        return $this->generator->parse(static::randomElement(static::$titleFormat));
    }

    /**
     * @example 'Mr.'
     *
     * @return string
     */
    public static function titleMale()
    {
        return static::randomElement(static::$titleMale);
    }

    /**
     * @example 'Mrs.'
     *
     * @return string
     */
    public static function titleFemale()
    {
        return static::randomElement(static::$titleFemale);
    }
}
