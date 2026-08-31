<?php

declare(strict_types=1);

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Carbon\Traits;

use Carbon\CarbonInterface;
use Carbon\Exceptions\InvalidTypeException;
use Carbon\Exceptions\NotLocaleAwareException;
use Carbon\Language;
use Carbon\Translator;
use Carbon\TranslatorStrongTypeInterface;
use Closure;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Trait Localization.
 *
 * Embed default and locale translators and translation base methods.
 */
trait Localization
{
    use StaticLocalization;

    /**
     * Specific translator of the current instance.
     */
    protected ?TranslatorInterface $localTranslator = null;

    /**
     * Return true if the current instance has its own translator.
     */
    public function hasLocalTranslator(): bool
    {
        return isset($this->localTranslator);
    }

    /**
     * Get the translator of the current instance or the default if none set.
     */
    public function getLocalTranslator(): TranslatorInterface
    {
        return $this->localTranslator ?? $this->transmitFactory(static fn () => static::getTranslator());
    }

    /**
     * Set the translator for the current instance.
     */
    public function setLocalTranslator(TranslatorInterface $translator): self
    {
        $this->localTranslator = $translator;

        return $this;
    }

    /**
     * Returns raw translation message for a given key.
     *
     * @param TranslatorInterface|null $translator the translator to use
     * @param string                   $key        key to find
     * @param string|null              $locale     current locale used if null
     * @param string|null              $default    default value if translation returns the key
     *
     * @return string|Closure|null
     */
    public static function getTranslationMessageWith($translator, string $key, ?string $locale = null, ?string $default = null)
    {
        if (!($translator instanceof TranslatorBagInterface && $translator instanceof TranslatorInterface)) {
            throw new InvalidTypeException(
                'Translator does not implement '.TranslatorInterface::class.' and '.TranslatorBagInterface::class.'. '.
                (\is_object($translator) ? \get_class($translator) : \gettype($translator)).' has been given.',
            );
        }

        if (!$locale && $translator instanceof LocaleAwareInterface) {
            $locale = $translator->getLocale();
        }

        $result = self::getFromCatalogue($translator, $translator->getCatalogue($locale), $key);

        return $result === $key ? $default : $result;
    }

    /**
     * Returns raw translation message for a given key.
     *
     * @param string              $key        key to find
     * @param string|null         $locale     current locale used if null
     * @param string|null         $default    default value if translation returns the key
     * @param TranslatorInterface $translator an optional translator to use
     *
     * @return string
     */
    public function getTranslationMessage(string $key, ?string $locale = null, ?string $default = null, $translator = null)
    {
        return static::getTranslationMessageWith($translator ?? $this->getLocalTranslator(), $key, $locale, $default);
    }

    /**
     * Translate using translation string or callback available.
     *
     * @param TranslatorInterface $translator an optional translator to use
     * @param string              $key        key to find
     * @param array               $parameters replacement parameters
     * @param int|float|null      $number     number if plural
     *
     * @return string
     */
    public static function translateWith(TranslatorInterface $translator, string $key, array $parameters = [], $number = null): string
    {
        $message = static::getTranslationMessageWith($translator, $key, null, $key);
        if ($message instanceof Closure) {
            return (string) $message(...array_values($parameters));
        }

        if ($number !== null) {
            $parameters['%count%'] = $number;
        }
        if (isset($parameters['%count%'])) {
            $parameters[':count'] = $parameters['%count%'];
        }

        return (string) $translator->trans($key, $parameters);
    }

    /**
     * Translate using translation string or callback available.
     *
     * @param string                   $key        key to find
     * @param array                    $parameters replacement parameters
     * @param string|int|float|null    $number     number if plural
     * @param TranslatorInterface|null $translator an optional translator to use
     * @param bool                     $altNumbers pass true to use alternative numbers
     *
     * @return string
     */
    public function translate(
        string $key,
        array $parameters = [],
        string|int|float|null $number = null,
        ?TranslatorInterface $translator = null,
        bool $altNumbers = false,
    ): string {
        $translation = static::translateWith($translator ?? $this->getLocalTranslator(), $key, $parameters, $number);

        if ($number !== null && $altNumbers) {
            return str_replace((string) $number, $this->translateNumber((int) $number), $translation);
        }

        return $translation;
    }

    /**
     * Returns the alternative number for a given integer if available in the current locale.
     *
     * @param int $number
     *
     * @return string
     */
    public function translateNumber(int $number): string
    {
        $translateKey = "alt_numbers.$number";
        $symbol = $this->translate($translateKey);

        if ($symbol !== $translateKey) {
            return $symbol;
        }

        if ($number > 99 && $this->translate('alt_numbers.99') !== 'alt_numbers.99') {
            $start = '';
            foreach ([10000, 1000, 100] as $exp) {
                $key = "alt_numbers_pow.$exp";
                if ($number >= $exp && $number < $exp * 10 && ($pow = $this->translate($key)) !== $key) {
                    $unit = floor($number / $exp);
                    $number -= $unit * $exp;
                    $start .= ($unit > 1 ? $this->translate("alt_numbers.$unit") : '').$pow;
                }
            }
            $result = '';
            while ($number) {
                $chunk = $number % 100;
                $result = $this->translate("alt_numbers.$chunk").$result;
                $number = floor($number / 100);
            }

            return "$start$result";
        }

        if ($number > 9 && $this->translate('alt_numbers.9') !== 'alt_numbers.9') {
            $result = '';
            while ($number) {
                $chunk = $number % 10;
                $result = $this->translate("alt_numbers.$chunk").$result;
                $number = floor($number / 10);
            }

            return $result;
        }

        return (string) $number;
    }

    /**
     * Translate a time string from a locale to an other.
     *
     * @param string      $timeString date/time/duration string to translate (may also contain English)
     * @param string|null $from       input locale of the $timeString parameter (`Carbon::getLocale()` by default)
     * @param string|null $to         output locale of the result returned (`"en"` by default)
     * @param int         $mode       specify what to translate with options:
     *                                - CarbonInterface::TRANSLATE_ALL (default)
     *                                - CarbonInterface::TRANSLATE_MONTHS
     *                                - CarbonInterface::TRANSLATE_DAYS
     *                                - CarbonInterface::TRANSLATE_UNITS
     *                                - CarbonInterface::TRANSLATE_MERIDIEM
     *                                You can use pipe to group: CarbonInterface::TRANSLATE_MONTHS | CarbonInterface::TRANSLATE_DAYS
     *
     * @return string
     */
    public static function translateTimeString(
        string $timeString,
        ?string $from = null,
        ?string $to = null,
        int $mode = CarbonInterface::TRANSLATE_ALL,
    ): string {
        // Fallback source and destination locales
        $from = $from ?: static::getLocale();
        $to = $to ?: CarbonInterface::DEFAULT_LOCALE;

        if ($from === $to) {
            return $timeString;
        }

        // Standardize apostrophe
        $timeString = strtr($timeString, ['’' => "'"]);

        $fromTranslations = [];
        $toTranslations = [];

        foreach (['from', 'to'] as $key) {
            $language = $$key;
            $translator = Translator::get($language);
            $translations = $translator->getMessages();

            if (!isset($translations[$language])) {
                return $timeString;
            }

            $translationKey = $key.'Translations';
            $messages = $translations[$language];
            $months = $messages['months'] ?? [];
            $weekdays = $messages['weekdays'] ?? [];
            $meridiem = $messages['meridiem'] ?? ['AM', 'PM'];

            if (isset($messages['ordinal_words'])) {
                $timeString = self::replaceOrdinalWords(
                    $timeString,
                    $key === 'from' ? array_flip($messages['ordinal_words']) : $messages['ordinal_words']
                );
            }

            $processList = static fn (array $list): array => $list;

            if ($key === 'from') {
                $processList = static fn (array $list): array => array_map(
                    static fn (string $words): string => implode('|', array_map(
                        static fn (string $word): string => preg_quote($word, '/'),
                        explode('|', $words),
                    )),
                    $list,
                );

                foreach (['months', 'weekdays'] as $variable) {
                    $list = $messages[$variable.'_standalone'] ?? null;

                    if ($list) {
                        foreach ($$variable as $index => &$name) {
                            $name .= '|'.$list[$index];
                        }
                    }
                }
            }

            $$translationKey = array_merge(
                $mode & CarbonInterface::TRANSLATE_MONTHS
                    ? $processList(self::getTranslationArray($months, static::MONTHS_PER_YEAR, $timeString))
                    : [],
                $mode & CarbonInterface::TRANSLATE_MONTHS
                    ? $processList(self::getTranslationArray($messages['months_short'] ?? [], static::MONTHS_PER_YEAR, $timeString))
                    : [],
                $mode & CarbonInterface::TRANSLATE_DAYS
                    ? $processList(self::getTranslationArray($weekdays, static::DAYS_PER_WEEK, $timeString))
                    : [],
                $mode & CarbonInterface::TRANSLATE_DAYS
                    ? $processList(self::getTranslationArray($messages['weekdays_short'] ?? [], static::DAYS_PER_WEEK, $timeString))
                    : [],
                $mode & CarbonInterface::TRANSLATE_DIFF ? self::translateWordsByKeys([
                    'diff_now',
                    'diff_today',
                    'diff_yesterday',
                    'diff_tomorrow',
                    'diff_before_yesterday',
                    'diff_after_tomorrow',
                ], $messages, $key) : [],
                $mode & CarbonInterface::TRANSLATE_UNITS ? self::translateWordsByKeys([
                    'year',
                    'month',
                    'week',
                    'day',
                    'hour',
                    'minute',
                    'second',
                ], $messages, $key) : [],
                $mode & CarbonInterface::TRANSLATE_MERIDIEM ? $processList(array_map(function ($hour) use ($meridiem) {
                    if (\is_array($meridiem)) {
                        return $meridiem[$hour < static::HOURS_PER_DAY / 2 ? 0 : 1];
                    }

                    return $meridiem($hour, 0, false);
                }, range(0, 23))) : [],
            );
        }

        // Make all dots optional
        $fromTranslations = array_map(
            static fn (string $word): string => strtr($word, [
                '\\.' => '\\.?',
            ]),
            $fromTranslations,
        );

        return substr(preg_replace_callback('/(?<=[\d\s+.\/,_-])('.implode('|', $fromTranslations).')(?=[\d\s+.\/,_-])/iu', function ($match) use ($fromTranslations, $toTranslations) {
            [$chunk] = $match;

            $index = array_search($chunk, $fromTranslations);

            if ($index !== false) {
                return $toTranslations[$index] ?? '';
            }

            foreach ($fromTranslations as $index => $word) {
                if (preg_match("/^$word\$/iu", $chunk)) {
                    return $toTranslations[$index] ?? '';
                }
            }

            return $chunk; // @codeCoverageIgnore
        }, " $timeString "), 1, -1);
    }

    /**
     * Translate a time string from the current locale (`$date->locale()`) to another one.
     *
     * @param string      $timeString time string to translate
     * @param string|null $to         output locale of the result returned ("en" by default)
     *
     * @return string
     */
    public function translateTimeStringTo(string $timeString, ?string $to = null): string
    {
        return static::translateTimeString($timeString, $this->getTranslatorLocale(), $to);
    }

    /**
     * Get/set the locale for the current instance.
     *
     * @param string|null $locale
     * @param string      ...$fallbackLocales
     *
     * @return ($locale is null ? string : static)
     */
    public function locale(?string $locale = null, string ...$fallbackLocales): static|string
    {
        if ($locale === null) {
            return $this->getTranslatorLocale();
        }

        if (!$this->localTranslator || $this->getTranslatorLocale($this->localTranslator) !== $locale) {
            $translator = Translator::get($locale);

            if (!empty($fallbackLocales)) {
                $translator->setFallbackLocales($fallbackLocales);

                foreach ($fallbackLocales as $fallbackLocale) {
                    $messages = Translator::get($fallbackLocale)->getMessages();

                    if (isset($messages[$fallbackLocale])) {
                        $translator->setMessages($fallbackLocale, $messages[$fallbackLocale]);
                    }
                }
            }

            $this->localTranslator = $translator;
        }

        return $this;
    }

    /**
     * Get the current translator locale.
     *
     * @return string
     */
    public static function getLocale(): string
    {
        return static::getLocaleAwareTranslator()->getLocale();
    }

    /**
     * Set the current translator locale and indicate if the source locale file exists.
     * Pass 'auto' as locale to use the closest language to the current LC_TIME locale.
     *
     * @param string $locale locale ex. en
     */
    public static function setLocale(string $locale): void
    {
        static::getLocaleAwareTranslator()->setLocale($locale);
    }

    /**
     * Set the fallback locale.
     *
     * @see https://symfony.com/doc/current/components/translation.html#fallback-locales
     *
     * @param string $locale
     */
    public static function setFallbackLocale(string $locale): void
    {
        $translator = static::getTranslator();

        if (method_exists($translator, 'setFallbackLocales')) {
            $fallbackLocales = [$locale];

            if (
                method_exists($translator, 'getFallbackLocales')
                && $fallbackLocales === $translator->getFallbackLocales()
            ) {
                return;
            }

            $translator->setFallbackLocales($fallbackLocales);

            if ($translator instanceof Translator) {
                $preferredLocale = $translator->getLocale();
                $fallbackMessages = [];
                $preferredMessages = $translator->getMessages($preferredLocale);

                foreach (Translator::get($locale)->getMessages()[$locale] ?? [] as $key => $value) {
                    if (
                        preg_match('/^(?:a_)?(.+)_(?:standalone|ago|from_now|before|after|short|min)$/', $key, $match)
                        && isset($preferredMessages[$match[1]])
                    ) {
                        continue;
                    }

                    $fallbackMessages[$key] = $value;
                }

                $translator->setMessages($preferredLocale, array_replace_recursive(
                    $translator->getMessages()[$locale] ?? [],
                    $fallbackMessages,
                    $preferredMessages,
                ));
            }
        }
    }

    /**
     * Get the fallback locale.
     *
     * @see https://symfony.com/doc/current/components/translation.html#fallback-locales
     */
    public static function getFallbackLocale(): ?string
    {
        $translator = static::getTranslator();

        if (method_exists($translator, 'getFallbackLocales')) {
            return $translator->getFallbackLocales()[0] ?? null;
        }

        return null;
    }

    /**
     * Set the current locale to the given, execute the passed function, reset the locale to previous one,
     * then return the result of the closure (or null if the closure was void).
     *
     * @param string   $locale locale ex. en
     * @param callable $func
     *
     * @return mixed
     */
    public static function executeWithLocale(string $locale, callable $func): mixed
    {
        $currentLocale = static::getLocale();
        static::setLocale($locale);
        $newLocale = static::getLocale();
        $result = $func(
            $newLocale === 'en' && strtolower(substr($locale, 0, 2)) !== 'en'
                ? false
                : $newLocale,
            static::getTranslator(),
        );
        static::setLocale($currentLocale);

        return $result;
    }

    /**
     * Returns true if the given locale is internally supported and has short-units support.
     * Support is considered enabled if either year, day or hour has a short variant translated.
     *
     * @param string $locale locale ex. en
     *
     * @return bool
     */
    public static function localeHasShortUnits(string $locale): bool
    {
        return static::executeWithLocale($locale, function ($newLocale, TranslatorInterface $translator) {
            return ($newLocale && (($y = static::translateWith($translator, 'y')) !== 'y' && $y !== static::translateWith($translator, 'year'))) || (
                ($y = static::translateWith($translator, 'd')) !== 'd' &&
                    $y !== static::translateWith($translator, 'day')
            ) || (
                ($y = static::translateWith($translator, 'h')) !== 'h' &&
                    $y !== static::translateWith($translator, 'hour')
            );
        });
    }

    /**
     * Returns true if the given locale is internally supported and has diff syntax support (ago, from now, before, after).
     * Support is considered enabled if the 4 sentences are translated in the given locale.
     *
     * @param string $locale locale ex. en
     *
     * @return bool
     */
    public static function localeHasDiffSyntax(string $locale): bool
    {
        return static::executeWithLocale($locale, function ($newLocale, TranslatorInterface $translator) {
            if (!$newLocale) {
                return false;
            }

            foreach (['ago', 'from_now', 'before', 'after'] as $key) {
                if ($translator instanceof TranslatorBagInterface &&
                    self::getFromCatalogue($translator, $translator->getCatalogue($newLocale), $key) instanceof Closure
                ) {
                    continue;
                }

                if ($translator->trans($key) === $key) {
                    return false;
                }
            }

            return true;
        });
    }

    /**
     * Returns true if the given locale is internally supported and has words for 1-day diff (just now, yesterday, tomorrow).
     * Support is considered enabled if the 3 words are translated in the given locale.
     *
     * @param string $locale locale ex. en
     *
     * @return bool
     */
    public static function localeHasDiffOneDayWords(string $locale): bool
    {
        return static::executeWithLocale($locale, function ($newLocale, TranslatorInterface $translator) {
            return $newLocale &&
                $translator->trans('diff_now') !== 'diff_now' &&
                $translator->trans('diff_yesterday') !== 'diff_yesterday' &&
                $translator->trans('diff_tomorrow') !== 'diff_tomorrow';
        });
    }

    /**
     * Returns true if the given locale is internally supported and has words for 2-days diff (before yesterday, after tomorrow).
     * Support is considered enabled if the 2 words are translated in the given locale.
     *
     * @param string $locale locale ex. en
     *
     * @return bool
     */
    public static function localeHasDiffTwoDayWords(string $locale): bool
    {
        return static::executeWithLocale($locale, function ($newLocale, TranslatorInterface $translator) {
            return $newLocale &&
                $translator->trans('diff_before_yesterday') !== 'diff_before_yesterday' &&
                $translator->trans('diff_after_tomorrow') !== 'diff_after_tomorrow';
        });
    }

    /**
     * Returns true if the given locale is internally supported and has period syntax support (X times, every X, from X, to X).
     * Support is considered enabled if the 4 sentences are translated in the given locale.
     *
     * @param string $locale locale ex. en
     *
     * @return bool
     */
    public static function localeHasPeriodSyntax($locale)
    {
        return static::executeWithLocale($locale, function ($newLocale, TranslatorInterface $translator) {
            return $newLocale &&
                $translator->trans('period_recurrences') !== 'period_recurrences' &&
                $translator->trans('period_interval') !== 'period_interval' &&
                $translator->trans('period_start_date') !== 'period_start_date' &&
                $translator->trans('period_end_date') !== 'period_end_date';
        });
    }

    /**
     * Returns the list of internally available locales and already loaded custom locales.
     * (It will ignore custom translator dynamic loading.)
     *
     * @return array
     */
    public static function getAvailableLocales(): array
    {
        $translator = static::getLocaleAwareTranslator();

        return $translator instanceof Translator
            ? $translator->getAvailableLocales()
            : [$translator->getLocale()];
    }

    /**
     * Returns list of Language object for each available locale. This object allow you to get the ISO name, native
     * name, region and variant of the locale.
     *
     * @return Language[]
     */
    public static function getAvailableLocalesInfo(): array
    {
        $languages = [];

        foreach (static::getAvailableLocales() as $id) {
            $languages[$id] = new Language($id);
        }

        return $languages;
    }

    /**
     * Get the locale of a given translator.
     *
     * If null or omitted, current local translator is used.
     * If no local translator is in use, current global translator is used.
     */
    protected function getTranslatorLocale($translator = null): ?string
    {
        if (\func_num_args() === 0) {
            $translator = $this->getLocalTranslator();
        }

        $translator = static::getLocaleAwareTranslator($translator);

        return $translator?->getLocale();
    }

    /**
     * Throw an error if passed object is not LocaleAwareInterface.
     *
     * @param LocaleAwareInterface|null $translator
     *
     * @return LocaleAwareInterface|null
     */
    protected static function getLocaleAwareTranslator($translator = null)
    {
        if (\func_num_args() === 0) {
            $translator = static::getTranslator();
        }

        if ($translator && !($translator instanceof LocaleAwareInterface || method_exists($translator, 'getLocale'))) {
            throw new NotLocaleAwareException($translator); // @codeCoverageIgnore
        }

        return $translator;
    }

    /**
     * @param mixed                                                    $translator
     * @param \Symfony\Component\Translation\MessageCatalogueInterface $catalogue
     *
     * @return mixed
     */
    private static function getFromCatalogue($translator, $catalogue, string $id, string $domain = 'messages')
    {
        return $translator instanceof TranslatorStrongTypeInterface
            ? $translator->getFromCatalogue($catalogue, $id, $domain)
            : $catalogue->get($id, $domain); // @codeCoverageIgnore
    }

    /**
     * Return the word cleaned from its translation codes.
     *
     * @param string $word
     *
     * @return string
     */
    private static function cleanWordFromTranslationString($word)
    {
        $word = str_replace([':count', '%count', ':time'], '', $word);
        $word = strtr($word, ['’' => "'"]);
        $word = preg_replace(
            '/\{(?:-?\d+(?:\.\d+)?|-?Inf)(?:,(?:-?\d+|-?Inf))?}|[\[\]](?:-?\d+(?:\.\d+)?|-?Inf)(?:,(?:-?\d+|-?Inf))?[\[\]]/',
            '',
            $word,
        );

        return trim($word);
    }

    /**
     * Translate a list of words.
     *
     * @param string[] $keys     keys to translate.
     * @param string[] $messages messages bag handling translations.
     * @param string   $key      'to' (to get the translation) or 'from' (to get the detection RegExp pattern).
     *
     * @return string[]
     */
    private static function translateWordsByKeys($keys, $messages, $key): array
    {
        return array_map(static function ($wordKey) use ($messages, $key) {
            $message = $key === 'from' && isset($messages[$wordKey.'_regexp'])
                ? $messages[$wordKey.'_regexp']
                : ($messages[$wordKey] ?? null);

            if (!$message) {
                return '>>DO NOT REPLACE<<';
            }

            $parts = explode('|', $message);

            return $key === 'to' || \count($parts) === 1
                ? self::cleanWordFromTranslationString(end($parts))
                : '(?:'.implode('|', array_map(static::cleanWordFromTranslationString(...), $parts)).')';
        }, $keys);
    }

    /**
     * Get an array of translations based on the current date.
     *
     * @param callable $translation
     * @param int      $length
     * @param string   $timeString
     *
     * @return string[]
     */
    private static function getTranslationArray($translation, $length, $timeString): array
    {
        $filler = '>>DO NOT REPLACE<<';

        if (\is_array($translation)) {
            return array_pad($translation, $length, $filler);
        }

        $list = [];
        $date = static::now();

        for ($i = 0; $i < $length; $i++) {
            $list[] = $translation($date, $timeString, $i) ?? $filler;
        }

        return $list;
    }

    private static function replaceOrdinalWords(string $timeString, array $ordinalWords): string
    {
        return preg_replace_callback('/(?<![a-z])[a-z]+(?![a-z])/i', function (array $match) use ($ordinalWords) {
            return $ordinalWords[mb_strtolower($match[0])] ?? $match[0];
        }, $timeString);
    }
}
