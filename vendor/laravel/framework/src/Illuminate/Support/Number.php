<?php

namespace Illuminate\Support;

use Illuminate\Support\Traits\Macroable;
use NumberFormatter;
use RuntimeException;

class Number
{
    use Macroable;

    /**
     * The current default locale.
     *
     * @var string
     */
    protected static $locale = 'en';

    /**
     * The current default currency.
     *
     * @var string
     */
    protected static $currency = 'USD';

    /**
     * Format the given number according to the current locale.
     *
     * @param  int|float  $number
     * @param  int|null  $precision
     * @param  int|null  $maxPrecision
     * @param  string|null  $locale
     * @return string|false
     */
    public static function format(int|float $number, ?int $precision = null, ?int $maxPrecision = null, ?string $locale = null)
    {
        static::ensureIntlExtensionIsInstalled();

        $formatter = new NumberFormatter($locale ?? static::$locale, NumberFormatter::DECIMAL);

        if (! is_null($maxPrecision)) {
            $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $maxPrecision);
        } elseif (! is_null($precision)) {
            $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $precision);
        }

        return $formatter->format($number);
    }

    /**
     * Parse the given string according to the specified format type.
     *
     * @param  string  $string
     * @param  int|null  $type
     * @param  string|null  $locale
     * @return int|float|false
     */
    public static function parse(string $string, ?int $type = NumberFormatter::TYPE_DOUBLE, ?string $locale = null): int|float|false
    {
        static::ensureIntlExtensionIsInstalled();

        $formatter = new NumberFormatter($locale ?? static::$locale, NumberFormatter::DECIMAL);

        return $formatter->parse($string, $type);
    }

    /**
     * Parse a string into an integer according to the specified locale.
     *
     * @param  string  $string
     * @param  string|null  $locale
     * @return int|false
     */
    public static function parseInt(string $string, ?string $locale = null): int|false
    {
        return self::parse($string, NumberFormatter::TYPE_INT32, $locale);
    }

    /**
     * Parse a string into a float according to the specified locale.
     *
     * @param  string  $string
     * @param  string|null  $locale
     * @return float|false
     */
    public static function parseFloat(string $string, ?string $locale = null): float|false
    {
        return self::parse($string, NumberFormatter::TYPE_DOUBLE, $locale);
    }

    /**
     * Spell out the given number in the given locale.
     *
     * @param  int|float  $number
     * @param  string|null  $locale
     * @param  int|null  $after
     * @param  int|null  $until
     * @return string
     */
    public static function spell(int|float $number, ?string $locale = null, ?int $after = null, ?int $until = null)
    {
        static::ensureIntlExtensionIsInstalled();

        if (! is_null($after) && $number <= $after) {
            return static::format($number, locale: $locale);
        }

        if (! is_null($until) && $number >= $until) {
            return static::format($number, locale: $locale);
        }

        $formatter = new NumberFormatter($locale ?? static::$locale, NumberFormatter::SPELLOUT);

        return $formatter->format($number);
    }

    /**
     * Convert the given number to ordinal form.
     *
     * @param  int|float  $number
     * @param  string|null  $locale
     * @return string
     */
    public static function ordinal(int|float $number, ?string $locale = null)
    {
        static::ensureIntlExtensionIsInstalled();

        $formatter = new NumberFormatter($locale ?? static::$locale, NumberFormatter::ORDINAL);

        return $formatter->format($number);
    }

    /**
     * Spell out the given number in the given locale in ordinal form.
     *
     * @param  int|float  $number
     * @param  string|null  $locale
     * @return string
     */
    public static function spellOrdinal(int|float $number, ?string $locale = null)
    {
        static::ensureIntlExtensionIsInstalled();

        $formatter = new NumberFormatter($locale ?? static::$locale, NumberFormatter::SPELLOUT);

        $formatter->setTextAttribute(NumberFormatter::DEFAULT_RULESET, '%spellout-ordinal');

        return $formatter->format($number);
    }

    /**
     * Convert the given number to its percentage equivalent.
     *
     * @param  int|float  $number
     * @param  int  $precision
     * @param  int|null  $maxPrecision
     * @param  string|null  $locale
     * @return string|false
     */
    public static function percentage(int|float $number, int $precision = 0, ?int $maxPrecision = null, ?string $locale = null)
    {
        static::ensureIntlExtensionIsInstalled();

        $formatter = new NumberFormatter($locale ?? static::$locale, NumberFormatter::PERCENT);

        if (! is_null($maxPrecision)) {
            $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $maxPrecision);
        } else {
            $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $precision);
        }

        return $formatter->format($number / 100);
    }

    /**
     * Convert the given number to its currency equivalent.
     *
     * @param  int|float  $number
     * @param  string  $in
     * @param  string|null  $locale
     * @param  int|null  $precision
     * @return string|false
     */
    public static function currency(int|float $number, string $in = '', ?string $locale = null, ?int $precision = null)
    {
        static::ensureIntlExtensionIsInstalled();

        $formatter = new NumberFormatter($locale ?? static::$locale, NumberFormatter::CURRENCY);

        if (! is_null($precision)) {
            $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $precision);
        }

        return $formatter->formatCurrency($number, ! empty($in) ? $in : static::$currency);
    }

    /**
     * Convert the given number to its file size equivalent.
     *
     * @param  int|float  $bytes
     * @param  int  $precision
     * @param  int|null  $maxPrecision
     * @return string
     */
    public static function fileSize(int|float $bytes, int $precision = 0, ?int $maxPrecision = null)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

        $unitCount = count($units);

        for ($i = 0; (abs($bytes) / 1024) > 0.9 && ($i < $unitCount - 1); $i++) {
            $bytes /= 1024;
        }

        return sprintf('%s %s', static::format($bytes, $precision, $maxPrecision), $units[$i]);
    }

    /**
     * Convert the number to its human-readable equivalent.
     *
     * @param  int|float  $number
     * @param  int  $precision
     * @param  int|null  $maxPrecision
     * @return string|false
     */
    public static function abbreviate(int|float $number, int $precision = 0, ?int $maxPrecision = null)
    {
        return static::forHumans($number, $precision, $maxPrecision, abbreviate: true);
    }

    /**
     * Convert the number to its human-readable equivalent.
     *
     * @param  int|float  $number
     * @param  int  $precision
     * @param  int|null  $maxPrecision
     * @param  bool  $abbreviate
     * @return string|false
     */
    public static function forHumans(int|float $number, int $precision = 0, ?int $maxPrecision = null, bool $abbreviate = false)
    {
        return static::summarize($number, $precision, $maxPrecision, $abbreviate ? [
            3 => 'K',
            6 => 'M',
            9 => 'B',
            12 => 'T',
            15 => 'Q',
        ] : [
            3 => ' thousand',
            6 => ' million',
            9 => ' billion',
            12 => ' trillion',
            15 => ' quadrillion',
        ]);
    }

    /**
     * Convert the number to its human-readable equivalent.
     *
     * @param  int|float  $number
     * @param  int  $precision
     * @param  int|null  $maxPrecision
     * @param  array  $units
     * @return string|false
     */
    protected static function summarize(int|float $number, int $precision = 0, ?int $maxPrecision = null, array $units = [])
    {
        if (empty($units)) {
            $units = [
                3 => 'K',
                6 => 'M',
                9 => 'B',
                12 => 'T',
                15 => 'Q',
            ];
        }

        switch (true) {
            case (float) $number === 0.0:
                return $precision > 0 ? static::format(0, $precision, $maxPrecision) : '0';
            case $number < 0:
                return sprintf('-%s', static::summarize(abs($number), $precision, $maxPrecision, $units));
            case $number >= 1e15:
                return sprintf('%s'.end($units), static::summarize($number / 1e15, $precision, $maxPrecision, $units));
        }

        $numberExponent = floor(log10($number));
        $displayExponent = $numberExponent - ($numberExponent % 3);
        $number /= pow(10, $displayExponent);

        return trim(sprintf('%s%s', static::format($number, $precision, $maxPrecision), $units[$displayExponent] ?? ''));
    }

    /**
     * Clamp the given number between the given minimum and maximum.
     *
     * @param  int|float  $number
     * @param  int|float  $min
     * @param  int|float  $max
     * @return int|float
     */
    public static function clamp(int|float $number, int|float $min, int|float $max)
    {
        return min(max($number, $min), $max);
    }

    /**
     * Split the given number into pairs of min/max values.
     *
     * @param  int|float  $to
     * @param  int|float  $by
     * @param  int|float  $start
     * @param  int|float  $offset
     * @return list<array{int|float, int|float}>
     */
    public static function pairs(int|float $to, int|float $by, int|float $start = 0, int|float $offset = 1)
    {
        if ($by == 0) {
            throw new \InvalidArgumentException('The $by argument must not be zero.');
        }

        $by = abs($by);

        $output = [];

        for ($lower = $start; $lower < $to; $lower += $by) {
            $upper = $lower + $by - $offset;

            if ($upper > $to) {
                $upper = $to;
            }

            $output[] = [$lower, $upper];
        }

        return $output;
    }

    /**
     * Remove any trailing zero digits after the decimal point of the given number.
     *
     * @param  int|float  $number
     * @return int|float
     */
    public static function trim(int|float $number)
    {
        if (is_infinite($number) || is_nan($number)) {
            return $number;
        }

        return json_decode(json_encode($number));
    }

    /**
     * Execute the given callback using the given locale.
     *
     * @template TReturn
     *
     * @param  string  $locale
     * @param  callable(): TReturn  $callback
     * @return TReturn
     */
    public static function withLocale(string $locale, callable $callback)
    {
        $previousLocale = static::$locale;

        static::useLocale($locale);

        try {
            return $callback();
        } finally {
            static::useLocale($previousLocale);
        }
    }

    /**
     * Execute the given callback using the given currency.
     *
     * @template TReturn
     *
     * @param  string  $currency
     * @param  callable(): TReturn  $callback
     * @return TReturn
     */
    public static function withCurrency(string $currency, callable $callback)
    {
        $previousCurrency = static::$currency;

        static::useCurrency($currency);

        try {
            return $callback();
        } finally {
            static::useCurrency($previousCurrency);
        }
    }

    /**
     * Set the default locale.
     *
     * @param  string  $locale
     * @return void
     */
    public static function useLocale(string $locale)
    {
        static::$locale = $locale;
    }

    /**
     * Set the default currency.
     *
     * @param  string  $currency
     * @return void
     */
    public static function useCurrency(string $currency)
    {
        static::$currency = $currency;
    }

    /**
     * Get the default locale.
     *
     * @return string
     */
    public static function defaultLocale()
    {
        return static::$locale;
    }

    /**
     * Get the default currency.
     *
     * @return string
     */
    public static function defaultCurrency()
    {
        return static::$currency;
    }

    /**
     * Ensure the "intl" PHP extension is installed.
     *
     * @return void
     *
     * @throws \RuntimeException
     */
    protected static function ensureIntlExtensionIsInstalled()
    {
        if (! extension_loaded('intl')) {
            $method = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'];

            throw new RuntimeException('The "intl" PHP extension is required to use the ['.$method.'] method.');
        }
    }
}
