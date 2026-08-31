<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Contracts\Translation;

use Symfony\Component\Translation\Exception\InvalidArgumentException;

/**
 * A trait to help implement TranslatorInterface and LocaleAwareInterface.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
trait TranslatorTrait
{
    private ?string $locale = null;

    /**
     * @return void
     */
    public function setLocale(string $locale)
    {
        $this->locale = $locale;
    }

    public function getLocale(): string
    {
        return $this->locale ?: (class_exists(\Locale::class) ? \Locale::getDefault() : 'en');
    }

    public function trans(?string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
    {
        if (null === $id || '' === $id) {
            return '';
        }

        foreach ($parameters as $k => $v) {
            if ($v instanceof TranslatableInterface) {
                $parameters[$k] = $v->trans($this, $locale);
            }
        }

        if (!isset($parameters['%count%']) || !is_numeric($parameters['%count%'])) {
            return strtr($id, $parameters);
        }

        $number = (float) $parameters['%count%'];
        $locale = $locale ?: $this->getLocale();

        $parts = [];
        if (preg_match('/^\|++$/', $id)) {
            $parts = explode('|', $id);
        } elseif (preg_match_all('/(?:\|\||[^\|])++/', $id, $matches)) {
            $parts = $matches[0];
        }

        $intervalRegexp = <<<'EOF'
            /^(?P<interval>
                ({\s*
                    (\-?\d+(\.\d+)?[\s*,\s*\-?\d+(\.\d+)?]*)
                \s*})

                    |

                (?P<left_delimiter>[\[\]])
                    \s*
                    (?P<left>-Inf|\-?\d+(\.\d+)?)
                    \s*,\s*
                    (?P<right>\+?Inf|\-?\d+(\.\d+)?)
                    \s*
                (?P<right_delimiter>[\[\]])
            )\s*(?P<message>.*?)$/xs
            EOF;

        $standardRules = [];
        foreach ($parts as $part) {
            $part = trim(str_replace('||', '|', $part));

            // try to match an explicit rule, then fallback to the standard ones
            if (preg_match($intervalRegexp, $part, $matches)) {
                if ($matches[2]) {
                    foreach (explode(',', $matches[3]) as $n) {
                        if ($number == $n) {
                            return strtr($matches['message'], $parameters);
                        }
                    }
                } else {
                    $leftNumber = '-Inf' === $matches['left'] ? -\INF : (float) $matches['left'];
                    $rightNumber = is_numeric($matches['right']) ? (float) $matches['right'] : \INF;

                    if (('[' === $matches['left_delimiter'] ? $number >= $leftNumber : $number > $leftNumber)
                        && (']' === $matches['right_delimiter'] ? $number <= $rightNumber : $number < $rightNumber)
                    ) {
                        return strtr($matches['message'], $parameters);
                    }
                }
            } elseif (preg_match('/^\w+\:\s*(.*?)$/', $part, $matches)) {
                $standardRules[] = $matches[1];
            } else {
                $standardRules[] = $part;
            }
        }

        $position = $this->getPluralizationRule($number, $locale);

        if (!isset($standardRules[$position])) {
            // when there's exactly one rule given, and that rule is a standard
            // rule, use this rule
            if (1 === \count($parts) && isset($standardRules[0])) {
                return strtr($standardRules[0], $parameters);
            }

            $message = \sprintf('Unable to choose a translation for "%s" with locale "%s" for value "%d". Double check that this translation has the correct plural options (e.g. "There is one apple|There are %%count%% apples").', $id, $locale, $number);

            if (class_exists(InvalidArgumentException::class)) {
                throw new InvalidArgumentException($message);
            }

            throw new \InvalidArgumentException($message);
        }

        return strtr($standardRules[$position], $parameters);
    }

    /**
     * Returns the plural position to use for the given locale and number.
     *
     * The plural rules are derived from code of the Zend Framework (2010-09-25),
     * which is subject to the new BSD license (http://framework.zend.com/license/new-bsd).
     * Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
     */
    private function getPluralizationRule(float $number, string $locale): int
    {
        $number = abs($number);

        return match ('pt_BR' !== $locale && 'en_US_POSIX' !== $locale && \strlen($locale) > 3 ? substr($locale, 0, strrpos($locale, '_')) : $locale) {
            'af',
            'bn',
            'bg',
            'ca',
            'da',
            'de',
            'el',
            'en',
            'en_US_POSIX',
            'eo',
            'es',
            'et',
            'eu',
            'fa',
            'fi',
            'fo',
            'fur',
            'fy',
            'gl',
            'gu',
            'ha',
            'he',
            'hu',
            'is',
            'it',
            'ku',
            'lb',
            'ml',
            'mn',
            'mr',
            'nah',
            'nb',
            'ne',
            'nl',
            'nn',
            'no',
            'oc',
            'om',
            'or',
            'pa',
            'pap',
            'ps',
            'pt',
            'so',
            'sq',
            'sv',
            'sw',
            'ta',
            'te',
            'tk',
            'ur',
            'zu' => (1 == $number) ? 0 : 1,
            'am',
            'bh',
            'fil',
            'fr',
            'gun',
            'hi',
            'hy',
            'ln',
            'mg',
            'nso',
            'pt_BR',
            'ti',
            'wa' => ($number < 2) ? 0 : 1,
            'be',
            'bs',
            'hr',
            'ru',
            'sh',
            'sr',
            'uk' => ((1 == $number % 10) && (11 != $number % 100)) ? 0 : ((($number % 10 >= 2) && ($number % 10 <= 4) && (($number % 100 < 10) || ($number % 100 >= 20))) ? 1 : 2),
            'cs',
            'sk' => (1 == $number) ? 0 : ((($number >= 2) && ($number <= 4)) ? 1 : 2),
            'ga' => (1 == $number) ? 0 : ((2 == $number) ? 1 : 2),
            'lt' => ((1 == $number % 10) && (11 != $number % 100)) ? 0 : ((($number % 10 >= 2) && (($number % 100 < 10) || ($number % 100 >= 20))) ? 1 : 2),
            'sl' => (1 == $number % 100) ? 0 : ((2 == $number % 100) ? 1 : (((3 == $number % 100) || (4 == $number % 100)) ? 2 : 3)),
            'mk' => (1 == $number % 10) ? 0 : 1,
            'mt' => (1 == $number) ? 0 : (((0 == $number) || (($number % 100 > 1) && ($number % 100 < 11))) ? 1 : ((($number % 100 > 10) && ($number % 100 < 20)) ? 2 : 3)),
            'lv' => (0 == $number) ? 0 : (((1 == $number % 10) && (11 != $number % 100)) ? 1 : 2),
            'pl' => (1 == $number) ? 0 : ((($number % 10 >= 2) && ($number % 10 <= 4) && (($number % 100 < 12) || ($number % 100 > 14))) ? 1 : 2),
            'cy' => (1 == $number) ? 0 : ((2 == $number) ? 1 : (((8 == $number) || (11 == $number)) ? 2 : 3)),
            'ro' => (1 == $number) ? 0 : (((0 == $number) || (($number % 100 > 0) && ($number % 100 < 20))) ? 1 : 2),
            'ar' => (0 == $number) ? 0 : ((1 == $number) ? 1 : ((2 == $number) ? 2 : ((($number % 100 >= 3) && ($number % 100 <= 10)) ? 3 : ((($number % 100 >= 11) && ($number % 100 <= 99)) ? 4 : 5)))),
            default => 0,
        };
    }
}
