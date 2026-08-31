<?php

namespace Faker\Provider\ka_GE;

class Company extends \Faker\Provider\Company
{
    protected static $companyPrefixes = [
        'შპს', 'შპს', 'შპს', 'სს', 'სს', 'სს', 'კს', 'სს კორპორაცია', 'იმ', 'სპს', 'კოოპერატივი',
    ];

    protected static $companyNameSuffixes = [
        'საბჭო', 'ექსპედიცია', 'პრომი', 'კომპლექსი', 'ავტო', 'ლიზინგი', 'თრასთი', 'ეიდი', 'პლუსი',
        'ლაბი', 'კავშირი', ' და კომპანია',
    ];

    protected static $companyElements = [
        'ცემ', 'გეო', 'ქარ', 'ქიმ', 'ლიფტ', 'ტელე', 'რადიო', 'ტრანს', 'ალმას', 'მეტრო',
        'მოტორ', 'ტექ', 'სანტექ', 'ელექტრო', 'რეაქტო', 'ტექსტილ', 'კაბელ', 'მავალ', 'ტელ',
        'ტექნო',
    ];

    protected static $companyNameFormats = [
        '{{companyPrefix}} {{companyNameElement}}{{companyNameSuffix}}',
        '{{companyPrefix}} {{companyNameElement}}{{companyNameElement}}{{companyNameSuffix}}',
        '{{companyPrefix}} {{companyNameElement}}{{companyNameElement}}{{companyNameElement}}{{companyNameSuffix}}',
        '{{companyPrefix}} {{companyNameElement}}{{companyNameElement}}{{companyNameElement}}{{companyNameSuffix}}',
    ];

    /**
     * @example 'იმ ელექტროალმასგეოსაბჭო'
     */
    public function company()
    {
        $format = static::randomElement(static::$companyNameFormats);

        return $this->generator->parse($format);
    }

    public static function companyPrefix()
    {
        return static::randomElement(static::$companyPrefixes);
    }

    public static function companyNameElement()
    {
        return static::randomElement(static::$companyElements);
    }

    public static function companyNameSuffix()
    {
        return static::randomElement(static::$companyNameSuffixes);
    }
}
