<?php

namespace Faker\Provider\kk_KZ;

use Faker\Provider\DateTime;

class Person extends \Faker\Provider\Person
{
    public const GENDER_MALE = 0;
    public const GENDER_FEMALE = 1;

    public const CENTURY_19TH = 0;
    public const CENTURY_20TH = 1;
    public const CENTURY_21ST = 2;

    public const MALE_CENTURY_19TH = 1;
    public const MALE_CENTURY_20TH = 3;
    public const MALE_CENTURY_21ST = 5;

    public const FEMALE_CENTURY_19TH = 2;
    public const FEMALE_CENTURY_20TH = 4;
    public const FEMALE_CENTURY_21ST = 6;

    /**
     * @var array
     */
    public static $firstSequenceBitWeights = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11];

    /**
     * @var array
     */
    public static $secondSequenceBitWeights = [3, 4, 5, 6, 7, 8, 9, 10, 11, 1, 2];

    /**
     * @var array
     */
    public static $genderCenturyMap = [
        self::GENDER_MALE => [
            self::CENTURY_19TH => self::MALE_CENTURY_19TH,
            self::CENTURY_20TH => self::MALE_CENTURY_20TH,
            self::CENTURY_21ST => self::MALE_CENTURY_21ST,
        ],
        self::GENDER_FEMALE => [
            self::CENTURY_19TH => self::FEMALE_CENTURY_19TH,
            self::CENTURY_20TH => self::FEMALE_CENTURY_20TH,
            self::CENTURY_21ST => self::FEMALE_CENTURY_21ST,
        ],
    ];

    /**
     * @see https://ru.wikipedia.org/wiki/%D0%9A%D0%B0%D0%B7%D0%B0%D1%85%D1%81%D0%BA%D0%B0%D1%8F_%D1%84%D0%B0%D0%BC%D0%B8%D0%BB%D0%B8%D1%8F
     *
     * @var array
     */
    protected static $maleNameFormats = [
        '{{lastName}}ұлы {{firstNameMale}}',
    ];

    /**
     * @see https://ru.wikipedia.org/wiki/%D0%9A%D0%B0%D0%B7%D0%B0%D1%85%D1%81%D0%BA%D0%B0%D1%8F_%D1%84%D0%B0%D0%BC%D0%B8%D0%BB%D0%B8%D1%8F
     *
     * @var array
     */
    protected static $femaleNameFormats = [
        '{{lastName}}қызы {{firstNameFemale}}',
    ];

    /**
     * @see http://koshpendi.kz/index.php/nomad/imena/
     *
     * @var array
     */
    protected static $firstNameMale = [
        'Аылғазы',
        'Әбдіқадыр',
        'Бабағожа',
        'Ғайса',
        'Дәмен',
        'Егізбек',
        'Жазылбек',
        'Зұлпықар',
        'Игісін',
        'Кәдіржан',
        'Қадырқан',
        'Латиф',
        'Мағаз',
        'Нармағамбет',
        'Оңалбай',
        'Өндіріс',
        'Пердебек',
        'Рақат',
        'Сағындық',
        'Танабай',
        'Уайыс',
        'Ұйықбай',
        'Үрімбай',
        'Файзрахман',
        'Хангелді',
        'Шаттық',
        'Ыстамбақы',
        'Ібни',
    ];

    /**
     * @see http://koshpendi.kz/index.php/nomad/imena/
     *
     * @var array
     */
    protected static $firstNameFemale = [
        'Асылтас',
        'Әужа',
        'Бүлдіршін',
        'Гүлшаш',
        'Ғафура',
        'Ділдә',
        'Еркежан',
        'Жібек',
        'Зылиқа',
        'Ирада',
        'Күнсұлу',
        'Қырмызы',
        'Ләтипа',
        'Мүштәри',
        'Нұршара',
        'Орынша',
        'Өрзия',
        'Перизат',
        'Рухия',
        'Сындыбала',
        'Тұрсынай',
        'Уәсима',
        'Ұрқия',
        'Үрия',
        'Фируза',
        'Хафиза',
        'Шырынгүл',
        'Ырысты',
        'Іңкәр',
    ];

    /**
     * @see http://koshpendi.kz/index.php/nomad/imena/
     * @see https://ru.wikipedia.org/wiki/%D0%9A%D0%B0%D0%B7%D0%B0%D1%85%D1%81%D0%BA%D0%B0%D1%8F_%D1%84%D0%B0%D0%BC%D0%B8%D0%BB%D0%B8%D1%8F
     *
     * @var array
     */
    protected static $lastName = [
        'Адырбай',
        'Әжібай',
        'Байбөрі',
        'Ғизат',
        'Ділдабек',
        'Ешмұхамбет',
        'Жігер',
        'Зікірия',
        'Иса',
        'Кунту',
        'Қыдыр',
        'Лұқпан',
        'Мышырбай',
        'Нысынбай',
        'Ошақбай',
        'Өтетілеу',
        'Пірәлі',
        'Рүстем',
        'Сырмұхамбет',
        'Тілеміс',
        'Уәлі',
        'Ұлықбек',
        'Үстем',
        'Фахир',
        'Хұсайын',
        'Шілдебай',
        'Ыстамбақы',
        'Ісмет',
    ];

    /**
     * @param int $year
     *
     * @return int|null
     */
    private static function getCenturyByYear($year)
    {
        if ($year >= 2000 && $year <= DateTime::year()) {
            return self::CENTURY_21ST;
        }

        if ($year >= 1900) {
            return self::CENTURY_20TH;
        }

        if ($year >= 1800) {
            return self::CENTURY_19TH;
        }

        return null;
    }

    /**
     * National Individual Identification Numbers
     *
     * @see   http://egov.kz/wps/portal/Content?contentPath=%2Fegovcontent%2Fcitizen_migration%2Fpassport_id_card%2Farticle%2Fiin_info&lang=en
     * @see   https://ru.wikipedia.org/wiki/%D0%98%D0%BD%D0%B4%D0%B8%D0%B2%D0%B8%D0%B4%D1%83%D0%B0%D0%BB%D1%8C%D0%BD%D1%8B%D0%B9_%D0%B8%D0%B4%D0%B5%D0%BD%D1%82%D0%B8%D1%84%D0%B8%D0%BA%D0%B0%D1%86%D0%B8%D0%BE%D0%BD%D0%BD%D1%8B%D0%B9_%D0%BD%D0%BE%D0%BC%D0%B5%D1%80
     *
     * @param \DateTime $birthDate
     * @param int       $gender
     *
     * @return string 12 digits, like 780322300455
     */
    public static function individualIdentificationNumber(\DateTime $birthDate = null, $gender = self::GENDER_MALE)
    {
        if (!$birthDate) {
            $birthDate = DateTime::dateTimeBetween();
        }

        do {
            $population = self::numberBetween(1000, 2000);
            $century = self::getCenturyByYear((int) $birthDate->format('Y'));

            $iin = $birthDate->format('ymd');
            $iin .= (string) self::$genderCenturyMap[$gender][$century];
            $iin .= (string) $population;
            $checksum = self::checkSum($iin);
        } while ($checksum === 10);

        return $iin . (string) $checksum;
    }

    /**
     * @param string $iinValue
     *
     * @return int
     */
    public static function checkSum($iinValue)
    {
        $controlDigit = self::getControlDigit($iinValue, self::$firstSequenceBitWeights);

        if ($controlDigit === 10) {
            return self::getControlDigit($iinValue, self::$secondSequenceBitWeights);
        }

        return $controlDigit;
    }

    /**
     * @param string $iinValue
     * @param array  $sequence
     *
     * @return int
     */
    protected static function getControlDigit($iinValue, $sequence)
    {
        $sum = 0;

        for ($i = 0; $i <= 10; ++$i) {
            $sum += (int) $iinValue[$i] * $sequence[$i];
        }

        return $sum % 11;
    }
}
