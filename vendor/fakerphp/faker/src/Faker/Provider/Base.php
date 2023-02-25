<?php

namespace Faker\Provider;

use Faker\DefaultGenerator;
use Faker\Generator;
use Faker\UniqueGenerator;
use Faker\ValidGenerator;

class Base
{
    /**
     * @var \Faker\Generator
     */
    protected $generator;

    /**
     * @var \Faker\UniqueGenerator
     */
    protected $unique;

    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * Returns a random number between 0 and 9
     *
     * @return int
     */
    public static function randomDigit()
    {
        return mt_rand(0, 9);
    }

    /**
     * Returns a random number between 1 and 9
     *
     * @return int
     */
    public static function randomDigitNotNull()
    {
        return mt_rand(1, 9);
    }

    /**
     * Generates a random digit, which cannot be $except
     *
     * @param int $except
     *
     * @return int
     */
    public static function randomDigitNot($except)
    {
        $result = self::numberBetween(0, 8);

        if ($result >= $except) {
            ++$result;
        }

        return $result;
    }

    /**
     * Returns a random integer with 0 to $nbDigits digits.
     *
     * The maximum value returned is mt_getrandmax()
     *
     * @param int  $nbDigits Defaults to a random number between 1 and 9
     * @param bool $strict   Whether the returned number should have exactly $nbDigits
     *
     * @example 79907610
     *
     * @return int
     */
    public static function randomNumber($nbDigits = null, $strict = false)
    {
        if (!is_bool($strict)) {
            throw new \InvalidArgumentException('randomNumber() generates numbers of fixed width. To generate numbers between two boundaries, use numberBetween() instead.');
        }

        if (null === $nbDigits) {
            $nbDigits = static::randomDigitNotNull();
        }
        $max = 10 ** $nbDigits - 1;

        if ($max > mt_getrandmax()) {
            throw new \InvalidArgumentException('randomNumber() can only generate numbers up to mt_getrandmax()');
        }

        if ($strict) {
            return mt_rand(10 ** ($nbDigits - 1), $max);
        }

        return mt_rand(0, $max);
    }

    /**
     * Return a random float number
     *
     * @param int       $nbMaxDecimals
     * @param float|int $min
     * @param float|int $max
     *
     * @example 48.8932
     *
     * @return float
     */
    public static function randomFloat($nbMaxDecimals = null, $min = 0, $max = null)
    {
        if (null === $nbMaxDecimals) {
            $nbMaxDecimals = static::randomDigit();
        }

        if (null === $max) {
            $max = static::randomNumber();

            if ($min > $max) {
                $max = $min;
            }
        }

        if ($min > $max) {
            $tmp = $min;
            $min = $max;
            $max = $tmp;
        }

        return round($min + mt_rand() / mt_getrandmax() * ($max - $min), $nbMaxDecimals);
    }

    /**
     * Returns a random number between $int1 and $int2 (any order)
     *
     * @param int $int1 default to 0
     * @param int $int2 defaults to 32 bit max integer, ie 2147483647
     *
     * @example 79907610
     *
     * @return int
     */
    public static function numberBetween($int1 = 0, $int2 = 2147483647)
    {
        $min = $int1 < $int2 ? $int1 : $int2;
        $max = $int1 < $int2 ? $int2 : $int1;

        return mt_rand($min, $max);
    }

    /**
     * Returns the passed value
     */
    public static function passthrough($value)
    {
        return $value;
    }

    /**
     * Returns a random letter from a to z
     *
     * @return string
     */
    public static function randomLetter()
    {
        return chr(mt_rand(97, 122));
    }

    /**
     * Returns a random ASCII character (excluding accents and special chars)
     *
     * @return string
     */
    public static function randomAscii()
    {
        return chr(mt_rand(33, 126));
    }

    /**
     * Returns randomly ordered subsequence of $count elements from a provided array
     *
     * @param array $array           Array to take elements from. Defaults to a-c
     * @param int   $count           Number of elements to take.
     * @param bool  $allowDuplicates Allow elements to be picked several times. Defaults to false
     *
     * @throws \LengthException When requesting more elements than provided
     *
     * @return array New array with $count elements from $array
     */
    public static function randomElements($array = ['a', 'b', 'c'], $count = 1, $allowDuplicates = false)
    {
        $traversables = [];

        if ($array instanceof \Traversable) {
            foreach ($array as $element) {
                $traversables[] = $element;
            }
        }

        $arr = count($traversables) ? $traversables : $array;

        $allKeys = array_keys($arr);
        $numKeys = count($allKeys);

        if (!$allowDuplicates && $numKeys < $count) {
            throw new \LengthException(sprintf('Cannot get %d elements, only %d in array', $count, $numKeys));
        }

        $highKey = $numKeys - 1;
        $keys = $elements = [];
        $numElements = 0;

        while ($numElements < $count) {
            $num = mt_rand(0, $highKey);

            if (!$allowDuplicates) {
                if (isset($keys[$num])) {
                    continue;
                }
                $keys[$num] = true;
            }

            $elements[] = $arr[$allKeys[$num]];
            ++$numElements;
        }

        return $elements;
    }

    /**
     * Returns a random element from a passed array
     *
     * @param array $array
     */
    public static function randomElement($array = ['a', 'b', 'c'])
    {
        if (!$array || ($array instanceof \Traversable && !count($array))) {
            return null;
        }
        $elements = static::randomElements($array, 1);

        return $elements[0];
    }

    /**
     * Returns a random key from a passed associative array
     *
     * @param array $array
     *
     * @return int|string|null
     */
    public static function randomKey($array = [])
    {
        if (!$array) {
            return null;
        }
        $keys = array_keys($array);

        return $keys[mt_rand(0, count($keys) - 1)];
    }

    /**
     * Returns a shuffled version of the argument.
     *
     * This function accepts either an array, or a string.
     *
     * @example $faker->shuffle([1, 2, 3]); // [2, 1, 3]
     * @example $faker->shuffle('hello, world'); // 'rlo,h eold!lw'
     *
     * @see shuffleArray()
     * @see shuffleString()
     *
     * @param array|string $arg The set to shuffle
     *
     * @return array|string The shuffled set
     */
    public static function shuffle($arg = '')
    {
        if (is_array($arg)) {
            return static::shuffleArray($arg);
        }

        if (is_string($arg)) {
            return static::shuffleString($arg);
        }

        throw new \InvalidArgumentException('shuffle() only supports strings or arrays');
    }

    /**
     * Returns a shuffled version of the array.
     *
     * This function does not mutate the original array. It uses the
     * Fisher–Yates algorithm, which is unbiased, together with a Mersenne
     * twister random generator. This function is therefore more random than
     * PHP's shuffle() function, and it is seedable.
     *
     * @see http://en.wikipedia.org/wiki/Fisher%E2%80%93Yates_shuffle
     *
     * @example $faker->shuffleArray([1, 2, 3]); // [2, 1, 3]
     *
     * @param array $array The set to shuffle
     *
     * @return array The shuffled set
     */
    public static function shuffleArray($array = [])
    {
        $shuffledArray = [];
        $i = 0;
        reset($array);

        foreach ($array as $key => $value) {
            if ($i == 0) {
                $j = 0;
            } else {
                $j = mt_rand(0, $i);
            }

            if ($j == $i) {
                $shuffledArray[] = $value;
            } else {
                $shuffledArray[] = $shuffledArray[$j];
                $shuffledArray[$j] = $value;
            }
            ++$i;
        }

        return $shuffledArray;
    }

    /**
     * Returns a shuffled version of the string.
     *
     * This function does not mutate the original string. It uses the
     * Fisher–Yates algorithm, which is unbiased, together with a Mersenne
     * twister random generator. This function is therefore more random than
     * PHP's shuffle() function, and it is seedable. Additionally, it is
     * UTF8 safe if the mb extension is available.
     *
     * @see http://en.wikipedia.org/wiki/Fisher%E2%80%93Yates_shuffle
     *
     * @example $faker->shuffleString('hello, world'); // 'rlo,h eold!lw'
     *
     * @param string $string   The set to shuffle
     * @param string $encoding The string encoding (defaults to UTF-8)
     *
     * @return string The shuffled set
     */
    public static function shuffleString($string = '', $encoding = 'UTF-8')
    {
        if (function_exists('mb_strlen')) {
            // UTF8-safe str_split()
            $array = [];
            $strlen = mb_strlen($string, $encoding);

            for ($i = 0; $i < $strlen; ++$i) {
                $array[] = mb_substr($string, $i, 1, $encoding);
            }
        } else {
            $array = str_split($string, 1);
        }

        return implode('', static::shuffleArray($array));
    }

    private static function replaceWildcard($string, $wildcard = '#', $callback = 'static::randomDigit')
    {
        if (($pos = strpos($string, $wildcard)) === false) {
            return $string;
        }

        for ($i = $pos, $last = strrpos($string, $wildcard, $pos) + 1; $i < $last; ++$i) {
            if ($string[$i] === $wildcard) {
                $string[$i] = call_user_func($callback);
            }
        }

        return $string;
    }

    /**
     * Replaces all hash sign ('#') occurrences with a random number
     * Replaces all percentage sign ('%') occurrences with a not null number
     *
     * @param string $string String that needs to bet parsed
     *
     * @return string
     */
    public static function numerify($string = '###')
    {
        // instead of using randomDigit() several times, which is slow,
        // count the number of hashes and generate once a large number
        $toReplace = [];

        if (($pos = strpos($string, '#')) !== false) {
            for ($i = $pos, $last = strrpos($string, '#', $pos) + 1; $i < $last; ++$i) {
                if ($string[$i] === '#') {
                    $toReplace[] = $i;
                }
            }
        }

        if ($nbReplacements = count($toReplace)) {
            $maxAtOnce = strlen((string) mt_getrandmax()) - 1;
            $numbers = '';
            $i = 0;

            while ($i < $nbReplacements) {
                $size = min($nbReplacements - $i, $maxAtOnce);
                $numbers .= str_pad(static::randomNumber($size), $size, '0', STR_PAD_LEFT);
                $i += $size;
            }

            for ($i = 0; $i < $nbReplacements; ++$i) {
                $string[$toReplace[$i]] = $numbers[$i];
            }
        }
        $string = self::replaceWildcard($string, '%', 'static::randomDigitNotNull');

        return $string;
    }

    /**
     * Replaces all question mark ('?') occurrences with a random letter
     *
     * @param string $string String that needs to bet parsed
     *
     * @return string
     */
    public static function lexify($string = '????')
    {
        return self::replaceWildcard($string, '?', 'static::randomLetter');
    }

    /**
     * Replaces hash signs ('#') and question marks ('?') with random numbers and letters
     * An asterisk ('*') is replaced with either a random number or a random letter
     *
     * @param string $string String that needs to bet parsed
     *
     * @return string
     */
    public static function bothify($string = '## ??')
    {
        $string = self::replaceWildcard($string, '*', static function () {
            return mt_rand(0, 1) ? '#' : '?';
        });

        return static::lexify(static::numerify($string));
    }

    /**
     * Replaces * signs with random numbers and letters and special characters
     *
     * @example $faker->asciify(''********'); // "s5'G!uC3"
     *
     * @param string $string String that needs to bet parsed
     *
     * @return string
     */
    public static function asciify($string = '****')
    {
        return preg_replace_callback('/\*/u', 'static::randomAscii', $string);
    }

    /**
     * Transforms a basic regular expression into a random string satisfying the expression.
     *
     * @example $faker->regexify('[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}'); // sm0@y8k96a.ej
     *
     * Regex delimiters '/.../' and begin/end markers '^...$' are ignored.
     *
     * Only supports a small subset of the regex syntax. For instance,
     * unicode, negated classes, unbounded ranges, subpatterns, back references,
     * assertions, recursive patterns, and comments are not supported. Escaping
     * support is extremely fragile.
     *
     * This method is also VERY slow. Use it only when no other formatter
     * can generate the fake data you want. For instance, prefer calling
     * `$faker->email` rather than `regexify` with the previous regular
     * expression.
     *
     * Also note than `bothify` can probably do most of what this method does,
     * but much faster. For instance, for a dummy email generation, try
     * `$faker->bothify('?????????@???.???')`.
     *
     * @see https://github.com/icomefromthenet/ReverseRegex for a more robust implementation
     *
     * @param string $regex A regular expression (delimiters are optional)
     *
     * @return string
     */
    public static function regexify($regex = '')
    {
        // ditch the anchors
        $regex = preg_replace('/^\/?\^?/', '', $regex);
        $regex = preg_replace('/\$?\/?$/', '', $regex);
        // All {2} become {2,2}
        $regex = preg_replace('/\{(\d+)\}/', '{\1,\1}', $regex);
        // Single-letter quantifiers (?, *, +) become bracket quantifiers ({0,1}, {0,rand}, {1, rand})
        $regex = preg_replace('/(?<!\\\)\?/', '{0,1}', $regex);
        $regex = preg_replace('/(?<!\\\)\*/', '{0,' . static::randomDigitNotNull() . '}', $regex);
        $regex = preg_replace('/(?<!\\\)\+/', '{1,' . static::randomDigitNotNull() . '}', $regex);
        // [12]{1,2} becomes [12] or [12][12]
        $regex = preg_replace_callback('/(\[[^\]]+\])\{(\d+),(\d+)\}/', static function ($matches) {
            return str_repeat($matches[1], Base::randomElement(range($matches[2], $matches[3])));
        }, $regex);
        // (12|34){1,2} becomes (12|34) or (12|34)(12|34)
        $regex = preg_replace_callback('/(\([^\)]+\))\{(\d+),(\d+)\}/', static function ($matches) {
            return str_repeat($matches[1], Base::randomElement(range($matches[2], $matches[3])));
        }, $regex);
        // A{1,2} becomes A or AA or \d{3} becomes \d\d\d
        $regex = preg_replace_callback('/(\\\?.)\{(\d+),(\d+)\}/', static function ($matches) {
            return str_repeat($matches[1], Base::randomElement(range($matches[2], $matches[3])));
        }, $regex);
        // (this|that) becomes 'this' or 'that'
        $regex = preg_replace_callback('/\((.*?)\)/', static function ($matches) {
            return Base::randomElement(explode('|', str_replace(['(', ')'], '', $matches[1])));
        }, $regex);
        // All A-F inside of [] become ABCDEF
        $regex = preg_replace_callback('/\[([^\]]+)\]/', static function ($matches) {
            return '[' . preg_replace_callback('/(\w|\d)\-(\w|\d)/', static function ($range) {
                return implode('', range($range[1], $range[2]));
            }, $matches[1]) . ']';
        }, $regex);
        // All [ABC] become B (or A or C)
        $regex = preg_replace_callback('/\[([^\]]+)\]/', static function ($matches) {
            $randomElement = Base::randomElement(str_split($matches[1]));
            //[.] should not be a random character, but a literal .
            return str_replace('.', '\.', $randomElement);
        }, $regex);
        // replace \d with number and \w with letter and . with ascii
        $regex = preg_replace_callback('/\\\w/', 'static::randomLetter', $regex);
        $regex = preg_replace_callback('/\\\d/', 'static::randomDigit', $regex);
        //replace . with ascii except backslash
        $regex = preg_replace_callback('/(?<!\\\)\./', static function () {
            $chr = static::asciify('*');

            if ($chr === '\\') {
                $chr .= '\\';
            }

            return $chr;
        }, $regex);
        // remove remaining single backslashes
        $regex = str_replace('\\\\', '[:escaped_backslash:]', $regex);
        $regex = str_replace('\\', '', $regex);
        $regex = str_replace('[:escaped_backslash:]', '\\', $regex);

        // phew
        return $regex;
    }

    /**
     * Converts string to lowercase.
     * Uses mb_string extension if available.
     *
     * @param string $string String that should be converted to lowercase
     *
     * @return string
     */
    public static function toLower($string = '')
    {
        return extension_loaded('mbstring') ? mb_strtolower($string, 'UTF-8') : strtolower($string);
    }

    /**
     * Converts string to uppercase.
     * Uses mb_string extension if available.
     *
     * @param string $string String that should be converted to uppercase
     *
     * @return string
     */
    public static function toUpper($string = '')
    {
        return extension_loaded('mbstring') ? mb_strtoupper($string, 'UTF-8') : strtoupper($string);
    }

    /**
     * Chainable method for making any formatter optional.
     *
     * @param float|int $weight Set the probability of receiving a null value.
     *                          "0" will always return null, "1" will always return the generator.
     *                          If $weight is an integer value, then the same system works
     *                          between 0 (always get false) and 100 (always get true).
     *
     * @return mixed|null
     */
    public function optional($weight = 0.5, $default = null)
    {
        // old system based on 0.1 <= $weight <= 0.9
        // TODO: remove in v2
        if ($weight > 0 && $weight < 1 && mt_rand() / mt_getrandmax() <= $weight) {
            return $this->generator;
        }

        // new system with percentage
        if (is_int($weight) && mt_rand(1, 100) <= $weight) {
            return $this->generator;
        }

        return new DefaultGenerator($default);
    }

    /**
     * Chainable method for making any formatter unique.
     *
     * <code>
     * // will never return twice the same value
     * $faker->unique()->randomElement(array(1, 2, 3));
     * </code>
     *
     * @param bool $reset      If set to true, resets the list of existing values
     * @param int  $maxRetries Maximum number of retries to find a unique value,
     *                         After which an OverflowException is thrown.
     *
     * @throws \OverflowException When no unique value can be found by iterating $maxRetries times
     *
     * @return UniqueGenerator A proxy class returning only non-existing values
     */
    public function unique($reset = false, $maxRetries = 10000)
    {
        if ($reset || !$this->unique) {
            $this->unique = new UniqueGenerator($this->generator, $maxRetries);
        }

        return $this->unique;
    }

    /**
     * Chainable method for forcing any formatter to return only valid values.
     *
     * The value validity is determined by a function passed as first argument.
     *
     * <code>
     * $values = array();
     * $evenValidator = function ($digit) {
     *   return $digit % 2 === 0;
     * };
     * for ($i=0; $i < 10; $i++) {
     *   $values []= $faker->valid($evenValidator)->randomDigit;
     * }
     * print_r($values); // [0, 4, 8, 4, 2, 6, 0, 8, 8, 6]
     * </code>
     *
     * @param Closure $validator  A function returning true for valid values
     * @param int     $maxRetries Maximum number of retries to find a unique value,
     *                            After which an OverflowException is thrown.
     *
     * @throws \OverflowException When no valid value can be found by iterating $maxRetries times
     *
     * @return ValidGenerator A proxy class returning only valid values
     */
    public function valid($validator = null, $maxRetries = 10000)
    {
        return new ValidGenerator($this->generator, $validator, $maxRetries);
    }
}
