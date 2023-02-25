<?php

namespace Faker;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

/**
 * @property string $citySuffix
 *
 * @method string citySuffix()
 *
 * @property string $streetSuffix
 *
 * @method string streetSuffix()
 *
 * @property string $buildingNumber
 *
 * @method string buildingNumber()
 *
 * @property string $city
 *
 * @method string city()
 *
 * @property string $streetName
 *
 * @method string streetName()
 *
 * @property string $streetAddress
 *
 * @method string streetAddress()
 *
 * @property string $postcode
 *
 * @method string postcode()
 *
 * @property string $address
 *
 * @method string address()
 *
 * @property string $country
 *
 * @method string country()
 *
 * @property float $latitude
 *
 * @method float latitude($min = -90, $max = 90)
 *
 * @property float $longitude
 *
 * @method float longitude($min = -180, $max = 180)
 *
 * @property float[] $localCoordinates
 *
 * @method float[] localCoordinates()
 *
 * @property int $randomDigitNotNull
 *
 * @method int randomDigitNotNull()
 *
 * @property mixed $passthrough
 *
 * @method mixed passthrough($value)
 *
 * @property string $randomLetter
 *
 * @method string randomLetter()
 *
 * @property string $randomAscii
 *
 * @method string randomAscii()
 *
 * @property array $randomElements
 *
 * @method array randomElements($array = ['a', 'b', 'c'], $count = 1, $allowDuplicates = false)
 *
 * @property mixed $randomElement
 *
 * @method mixed randomElement($array = ['a', 'b', 'c'])
 *
 * @property int|string|null $randomKey
 *
 * @method int|string|null randomKey($array = [])
 *
 * @property array|string $shuffle
 *
 * @method array|string shuffle($arg = '')
 *
 * @property array $shuffleArray
 *
 * @method array shuffleArray($array = [])
 *
 * @property string $shuffleString
 *
 * @method string shuffleString($string = '', $encoding = 'UTF-8')
 *
 * @property string $numerify
 *
 * @method string numerify($string = '###')
 *
 * @property string $lexify
 *
 * @method string lexify($string = '????')
 *
 * @property string $bothify
 *
 * @method string bothify($string = '## ??')
 *
 * @property string $asciify
 *
 * @method string asciify($string = '****')
 *
 * @property string $regexify
 *
 * @method string regexify($regex = '')
 *
 * @property string $toLower
 *
 * @method string toLower($string = '')
 *
 * @property string $toUpper
 *
 * @method string toUpper($string = '')
 *
 * @property mixed $optional
 *
 * @method mixed optional($weight = null, $default = null)
 *
 * @property UniqueGenerator $unique
 *
 * @method UniqueGenerator unique($reset = false, $maxRetries = 10000)
 *
 * @property ValidGenerator $valid
 *
 * @method ValidGenerator valid($validator = null, $maxRetries = 10000)
 *
 * @property int $biasedNumberBetween
 *
 * @method int biasedNumberBetween($min = 0, $max = 100, $function = 'sqrt')
 *
 * @property string $hexColor
 *
 * @method string hexColor()
 *
 * @property string $safeHexColor
 *
 * @method string safeHexColor()
 *
 * @property array $rgbColorAsArray
 *
 * @method array rgbColorAsArray()
 *
 * @property string $rgbColor
 *
 * @method string rgbColor()
 *
 * @property string $rgbCssColor
 *
 * @method string rgbCssColor()
 *
 * @property string $rgbaCssColor
 *
 * @method string rgbaCssColor()
 *
 * @property string $safeColorName
 *
 * @method string safeColorName()
 *
 * @property string $colorName
 *
 * @method string colorName()
 *
 * @property string $hslColor
 *
 * @method string hslColor()
 *
 * @property array $hslColorAsArray
 *
 * @method array hslColorAsArray()
 *
 * @property string $company
 *
 * @method string company()
 *
 * @property string $companySuffix
 *
 * @method string companySuffix()
 *
 * @property string $jobTitle
 *
 * @method string jobTitle()
 *
 * @property int $unixTime
 *
 * @method int unixTime($max = 'now')
 *
 * @property \DateTime $dateTime
 *
 * @method \DateTime dateTime($max = 'now', $timezone = null)
 *
 * @property \DateTime $dateTimeAD
 *
 * @method \DateTime dateTimeAD($max = 'now', $timezone = null)
 *
 * @property string $iso8601
 *
 * @method string iso8601($max = 'now')
 *
 * @property string $date
 *
 * @method string date($format = 'Y-m-d', $max = 'now')
 *
 * @property string $time
 *
 * @method string time($format = 'H:i:s', $max = 'now')
 *
 * @property \DateTime $dateTimeBetween
 *
 * @method \DateTime dateTimeBetween($startDate = '-30 years', $endDate = 'now', $timezone = null)
 *
 * @property \DateTime $dateTimeInInterval
 *
 * @method \DateTime dateTimeInInterval($date = '-30 years', $interval = '+5 days', $timezone = null)
 *
 * @property \DateTime $dateTimeThisCentury
 *
 * @method \DateTime dateTimeThisCentury($max = 'now', $timezone = null)
 *
 * @property \DateTime $dateTimeThisDecade
 *
 * @method \DateTime dateTimeThisDecade($max = 'now', $timezone = null)
 *
 * @property \DateTime $dateTimeThisYear
 *
 * @method \DateTime dateTimeThisYear($max = 'now', $timezone = null)
 *
 * @property \DateTime $dateTimeThisMonth
 *
 * @method \DateTime dateTimeThisMonth($max = 'now', $timezone = null)
 *
 * @property string $amPm
 *
 * @method string amPm($max = 'now')
 *
 * @property string $dayOfMonth
 *
 * @method string dayOfMonth($max = 'now')
 *
 * @property string $dayOfWeek
 *
 * @method string dayOfWeek($max = 'now')
 *
 * @property string $month
 *
 * @method string month($max = 'now')
 *
 * @property string $monthName
 *
 * @method string monthName($max = 'now')
 *
 * @property string $year
 *
 * @method string year($max = 'now')
 *
 * @property string $century
 *
 * @method string century()
 *
 * @property string $timezone
 *
 * @method string timezone()
 *
 * @property void $setDefaultTimezone
 *
 * @method void setDefaultTimezone($timezone = null)
 *
 * @property string $getDefaultTimezone
 *
 * @method string getDefaultTimezone()
 *
 * @property string $file
 *
 * @method string file($sourceDirectory = '/tmp', $targetDirectory = '/tmp', $fullPath = true)
 *
 * @property string $randomHtml
 *
 * @method string randomHtml($maxDepth = 4, $maxWidth = 4)
 *
 * @property string $imageUrl
 *
 * @method string imageUrl($width = 640, $height = 480, $category = null, $randomize = true, $word = null, $gray = false)
 *
 * @property string $image
 *
 * @method string image($dir = null, $width = 640, $height = 480, $category = null, $fullPath = true, $randomize = true, $word = null, $gray = false)
 *
 * @property string $email
 *
 * @method string email()
 *
 * @property string $safeEmail
 *
 * @method string safeEmail()
 *
 * @property string $freeEmail
 *
 * @method string freeEmail()
 *
 * @property string $companyEmail
 *
 * @method string companyEmail()
 *
 * @property string $freeEmailDomain
 *
 * @method string freeEmailDomain()
 *
 * @property string $safeEmailDomain
 *
 * @method string safeEmailDomain()
 *
 * @property string $userName
 *
 * @method string userName()
 *
 * @property string $password
 *
 * @method string password($minLength = 6, $maxLength = 20)
 *
 * @property string $domainName
 *
 * @method string domainName()
 *
 * @property string $domainWord
 *
 * @method string domainWord()
 *
 * @property string $tld
 *
 * @method string tld()
 *
 * @property string $url
 *
 * @method string url()
 *
 * @property string $slug
 *
 * @method string slug($nbWords = 6, $variableNbWords = true)
 *
 * @property string $ipv4
 *
 * @method string ipv4()
 *
 * @property string $ipv6
 *
 * @method string ipv6()
 *
 * @property string $localIpv4
 *
 * @method string localIpv4()
 *
 * @property string $macAddress
 *
 * @method string macAddress()
 *
 * @property string $word
 *
 * @method string word()
 *
 * @property array|string $words
 *
 * @method array|string words($nb = 3, $asText = false)
 *
 * @property string $sentence
 *
 * @method string sentence($nbWords = 6, $variableNbWords = true)
 *
 * @property array|string $sentences
 *
 * @method array|string sentences($nb = 3, $asText = false)
 *
 * @property string $paragraph
 *
 * @method string paragraph($nbSentences = 3, $variableNbSentences = true)
 *
 * @property array|string $paragraphs
 *
 * @method array|string paragraphs($nb = 3, $asText = false)
 *
 * @property string $text
 *
 * @method string text($maxNbChars = 200)
 *
 * @property bool $boolean
 *
 * @method bool boolean($chanceOfGettingTrue = 50)
 *
 * @property string $md5
 *
 * @method string md5()
 *
 * @property string $sha1
 *
 * @method string sha1()
 *
 * @property string $sha256
 *
 * @method string sha256()
 *
 * @property string $locale
 *
 * @method string locale()
 *
 * @property string $countryCode
 *
 * @method string countryCode()
 *
 * @property string $countryISOAlpha3
 *
 * @method string countryISOAlpha3()
 *
 * @property string $languageCode
 *
 * @method string languageCode()
 *
 * @property string $currencyCode
 *
 * @method string currencyCode()
 *
 * @property string $emoji
 *
 * @method string emoji()
 *
 * @property string $creditCardType
 *
 * @method string creditCardType()
 *
 * @property string $creditCardNumber
 *
 * @method string creditCardNumber($type = null, $formatted = false, $separator = '-')
 *
 * @property \DateTime $creditCardExpirationDate
 *
 * @method \DateTime creditCardExpirationDate($valid = true)
 *
 * @property string $creditCardExpirationDateString
 *
 * @method string creditCardExpirationDateString($valid = true, $expirationDateFormat = null)
 *
 * @property array $creditCardDetails
 *
 * @method array creditCardDetails($valid = true)
 *
 * @property string $iban
 *
 * @method string iban($countryCode = null, $prefix = '', $length = null)
 *
 * @property string $swiftBicNumber
 *
 * @method string swiftBicNumber()
 *
 * @property string $name
 *
 * @method string name($gender = null)
 *
 * @property string $firstName
 *
 * @method string firstName($gender = null)
 *
 * @property string $firstNameMale
 *
 * @method string firstNameMale()
 *
 * @property string $firstNameFemale
 *
 * @method string firstNameFemale()
 *
 * @property string $lastName
 *
 * @method string lastName()
 *
 * @property string $title
 *
 * @method string title($gender = null)
 *
 * @property string $titleMale
 *
 * @method string titleMale()
 *
 * @property string $titleFemale
 *
 * @method string titleFemale()
 *
 * @property string $phoneNumber
 *
 * @method string phoneNumber()
 *
 * @property string $e164PhoneNumber
 *
 * @method string e164PhoneNumber()
 *
 * @property int $imei
 *
 * @method int imei()
 *
 * @property string $realText
 *
 * @method string realText($maxNbChars = 200, $indexSize = 2)
 *
 * @property string $realTextBetween
 *
 * @method string realTextBetween($minNbChars = 160, $maxNbChars = 200, $indexSize = 2)
 *
 * @property string $macProcessor
 *
 * @method string macProcessor()
 *
 * @property string $linuxProcessor
 *
 * @method string linuxProcessor()
 *
 * @property string $userAgent
 *
 * @method string userAgent()
 *
 * @property string $chrome
 *
 * @method string chrome()
 *
 * @property string $firefox
 *
 * @method string firefox()
 *
 * @property string $safari
 *
 * @method string safari()
 *
 * @property string $opera
 *
 * @method string opera()
 *
 * @property string $internetExplorer
 *
 * @method string internetExplorer()
 *
 * @property string $windowsPlatformToken
 *
 * @method string windowsPlatformToken()
 *
 * @property string $macPlatformToken
 *
 * @method string macPlatformToken()
 *
 * @property string $linuxPlatformToken
 *
 * @method string linuxPlatformToken()
 *
 * @property string $uuid
 *
 * @method string uuid()
 */
class Generator
{
    protected $providers = [];
    protected $formatters = [];

    private $container;

    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container ?: Extension\ContainerBuilder::getDefault();
    }

    /**
     * @template T of Extension\Extension
     *
     * @param class-string<T> $id
     *
     * @throws ContainerExceptionInterface
     * @throws Extension\ExtensionNotFound
     *
     * @return T
     */
    public function ext(string $id): Extension\Extension
    {
        if (!$this->container->has($id)) {
            throw new Extension\ExtensionNotFound(sprintf(
                'No Faker extension with id "%s" was loaded.',
                $id
            ));
        }

        $extension = $this->container->get($id);

        if ($extension instanceof Extension\GeneratorAwareExtension) {
            $extension = $extension->withGenerator($this);
        }

        return $extension;
    }

    public function addProvider($provider)
    {
        array_unshift($this->providers, $provider);
    }

    public function getProviders()
    {
        return $this->providers;
    }

    public function seed($seed = null)
    {
        if ($seed === null) {
            mt_srand();
        } else {
            mt_srand((int) $seed, MT_RAND_PHP);
        }
    }

    public function format($format, $arguments = [])
    {
        return call_user_func_array($this->getFormatter($format), $arguments);
    }

    /**
     * @param string $format
     *
     * @return callable
     */
    public function getFormatter($format)
    {
        if (isset($this->formatters[$format])) {
            return $this->formatters[$format];
        }

        if (method_exists($this, $format)) {
            $this->formatters[$format] = [$this, $format];

            return $this->formatters[$format];
        }

        // "Faker\Core\Barcode->ean13"
        if (preg_match('|^([a-zA-Z0-9\\\]+)->([a-zA-Z0-9]+)$|', $format, $matches)) {
            $this->formatters[$format] = [$this->ext($matches[1]), $matches[2]];

            return $this->formatters[$format];
        }

        foreach ($this->providers as $provider) {
            if (method_exists($provider, $format)) {
                $this->formatters[$format] = [$provider, $format];

                return $this->formatters[$format];
            }
        }

        throw new \InvalidArgumentException(sprintf('Unknown format "%s"', $format));
    }

    /**
     * Replaces tokens ('{{ tokenName }}') with the result from the token method call
     *
     * @param string $string String that needs to bet parsed
     *
     * @return string
     */
    public function parse($string)
    {
        $callback = function ($matches) {
            return $this->format($matches[1]);
        };

        return preg_replace_callback('/\{\{\s?(\w+)\s?\}\}/u', $callback, $string);
    }

    /**
     * Get a random MIME type
     *
     * @example 'video/avi'
     */
    public function mimeType()
    {
        return $this->ext(Extension\FileExtension::class)->mimeType();
    }

    /**
     * Get a random file extension (without a dot)
     *
     * @example avi
     */
    public function fileExtension()
    {
        return $this->ext(Extension\FileExtension::class)->extension();
    }

    /**
     * Get a full path to a new real file on the system.
     */
    public function filePath()
    {
        return $this->ext(Extension\FileExtension::class)->filePath();
    }

    /**
     * Get an actual blood type
     *
     * @example 'AB'
     */
    public function bloodType(): string
    {
        return $this->ext(Extension\BloodExtension::class)->bloodType();
    }

    /**
     * Get a random resis value
     *
     * @example '+'
     */
    public function bloodRh(): string
    {
        return $this->ext(Extension\BloodExtension::class)->bloodRh();
    }

    /**
     * Get a full blood group
     *
     * @example 'AB+'
     */
    public function bloodGroup(): string
    {
        return $this->ext(Extension\BloodExtension::class)->bloodGroup();
    }

    /**
     * Get a random EAN13 barcode.
     *
     * @example '4006381333931'
     */
    public function ean13(): string
    {
        return $this->ext(Extension\BarcodeExtension::class)->ean13();
    }

    /**
     * Get a random EAN8 barcode.
     *
     * @example '73513537'
     */
    public function ean8(): string
    {
        return $this->ext(Extension\BarcodeExtension::class)->ean8();
    }

    /**
     * Get a random ISBN-10 code
     *
     * @see http://en.wikipedia.org/wiki/International_Standard_Book_Number
     *
     * @example '4881416324'
     */
    public function isbn10(): string
    {
        return $this->ext(Extension\BarcodeExtension::class)->isbn10();
    }

    /**
     * Get a random ISBN-13 code
     *
     * @see http://en.wikipedia.org/wiki/International_Standard_Book_Number
     *
     * @example '9790404436093'
     */
    public function isbn13(): string
    {
        return $this->ext(Extension\BarcodeExtension::class)->isbn13();
    }

    /**
     * Returns a random number between $int1 and $int2 (any order)
     *
     * @example 79907610
     */
    public function numberBetween($int1 = 0, $int2 = 2147483647): int
    {
        return $this->ext(Extension\NumberExtension::class)->numberBetween((int) $int1, (int) $int2);
    }

    /**
     * Returns a random number between 0 and 9
     */
    public function randomDigit(): int
    {
        return $this->ext(Extension\NumberExtension::class)->randomDigit();
    }

    /**
     * Generates a random digit, which cannot be $except
     */
    public function randomDigitNot($except): int
    {
        return $this->ext(Extension\NumberExtension::class)->randomDigitNot((int) $except);
    }

    /**
     * Returns a random number between 1 and 9
     */
    public function randomDigitNotZero(): int
    {
        return $this->ext(Extension\NumberExtension::class)->randomDigitNotZero();
    }

    /**
     * Return a random float number
     *
     * @example 48.8932
     */
    public function randomFloat($nbMaxDecimals = null, $min = 0, $max = null): float
    {
        return $this->ext(Extension\NumberExtension::class)->randomFloat(
            $nbMaxDecimals !== null ? (int) $nbMaxDecimals : null,
            (float) $min,
            $max !== null ? (float) $max : null
        );
    }

    /**
     * Returns a random integer with 0 to $nbDigits digits.
     *
     * The maximum value returned is mt_getrandmax()
     *
     * @param int|null $nbDigits Defaults to a random number between 1 and 9
     * @param bool     $strict   Whether the returned number should have exactly $nbDigits
     *
     * @example 79907610
     */
    public function randomNumber($nbDigits = null, $strict = false): int
    {
        return $this->ext(Extension\NumberExtension::class)->randomNumber(
            $nbDigits !== null ? (int) $nbDigits : null,
            (bool) $strict
        );
    }

    /**
     * Get a version number in semantic versioning syntax 2.0.0. (https://semver.org/spec/v2.0.0.html)
     *
     * @param bool $preRelease Pre release parts may be randomly included
     * @param bool $build      Build parts may be randomly included
     *
     * @example 1.0.0
     * @example 1.0.0-alpha.1
     * @example 1.0.0-alpha.1+b71f04d
     */
    public function semver(bool $preRelease = false, bool $build = false): string
    {
        return $this->ext(Extension\VersionExtension::class)->semver($preRelease, $build);
    }

    /**
     * @deprecated
     */
    protected function callFormatWithMatches($matches)
    {
        trigger_deprecation('fakerphp/faker', '1.14', 'Protected method "callFormatWithMatches()" is deprecated and will be removed.');

        return $this->format($matches[1]);
    }

    /**
     * @param string $attribute
     *
     * @deprecated Use a method instead.
     */
    public function __get($attribute)
    {
        trigger_deprecation('fakerphp/faker', '1.14', 'Accessing property "%s" is deprecated, use "%s()" instead.', $attribute, $attribute);

        return $this->format($attribute);
    }

    /**
     * @param string $method
     * @param array  $attributes
     */
    public function __call($method, $attributes)
    {
        return $this->format($method, $attributes);
    }

    public function __destruct()
    {
        $this->seed();
    }

    public function __wakeup()
    {
        $this->formatters = [];
    }
}
