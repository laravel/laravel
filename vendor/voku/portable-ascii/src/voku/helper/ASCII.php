<?php

declare(strict_types=1);

namespace voku\helper;

/**
 * ## 🇷🇺 Русским гражданам
 * В Украине сейчас идет война. Силами РФ наносятся удары по гражданской инфраструктуре в [Харькове][1], [Киеве][2], [Чернигове][3], [Сумах][4], [Ирпене][5] и десятках других городов. Гибнут люди - и гражданское население, и военные, в том числе российские призывники, которых бросили воевать. Чтобы лишить собственный народ доступа к информации, правительство РФ запретило называть войну войной, закрыло независимые СМИ и принимает сейчас ряд диктаторских законов. Эти законы призваны заткнуть рот всем, кто против войны. За обычный призыв к миру сейчас можно получить несколько лет тюрьмы.
 * Не молчите! Молчание - знак вашего согласия с политикой российского правительства.
 *  **Вы можете сделать выбор НЕ МОЛЧАТЬ.**
 * ---
 * ## 🇺🇸 To people of Russia
 * There is a war in Ukraine right now. The forces of the Russian Federation are attacking civilian infrastructure in [Kharkiv][1], [Kyiv][2], [Chernihiv][3], [Sumy][4], [Irpin][5] and dozens of other cities. People are dying – both civilians and military servicemen, including Russian conscripts who were thrown into the fighting. In order to deprive its own people of access to information, the government of the Russian Federation has forbidden calling a war a war, shut down independent media and is passing a number of dictatorial laws. These laws are meant to silence all those who are against war. You can be jailed for multiple years for simply calling for peace.
 * Do not be silent! Silence is a sign that you accept the Russian government's policy.
 * **You can choose NOT TO BE SILENT.**
 * ---
 * - [1] https://cloudfront-us-east-2.images.arcpublishing.com/reuters/P7K2MSZDGFMIJPDD7CI2GIROJI.jpg "Kharkiv under attack"
 * - [2] https://gdb.voanews.com/01bd0000-0aff-0242-fad0-08d9fc92c5b3_cx0_cy5_cw0_w1023_r1_s.jpg "Kyiv under attack"
 * - [3] https://ichef.bbci.co.uk/news/976/cpsprodpb/163DD/production/_123510119_hi074310744.jpg "Chernihiv under attack"
 * - [4] https://www.youtube.com/watch?v=8K-bkqKKf2A "Sumy under attack"
 * - [5] https://cloudfront-us-east-2.images.arcpublishing.com/reuters/K4MTMLEHTRKGFK3GSKAT4GR3NE.jpg "Irpin under attack"
 *
 * @psalm-immutable
 */
final class ASCII
{
    //
    // INFO: https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
    //

    const UZBEK_LANGUAGE_CODE = 'uz';

    const TURKMEN_LANGUAGE_CODE = 'tk';

    const THAI_LANGUAGE_CODE = 'th';

    const PASHTO_LANGUAGE_CODE = 'ps';

    const ORIYA_LANGUAGE_CODE = 'or';

    const MONGOLIAN_LANGUAGE_CODE = 'mn';

    const KOREAN_LANGUAGE_CODE = 'ko';

    const KIRGHIZ_LANGUAGE_CODE = 'ky';

    const ARMENIAN_LANGUAGE_CODE = 'hy';

    const BENGALI_LANGUAGE_CODE = 'bn';

    const BELARUSIAN_LANGUAGE_CODE = 'be';

    const AMHARIC_LANGUAGE_CODE = 'am';

    const JAPANESE_LANGUAGE_CODE = 'ja';

    const CHINESE_LANGUAGE_CODE = 'zh';

    const DUTCH_LANGUAGE_CODE = 'nl';

    const ITALIAN_LANGUAGE_CODE = 'it';

    const MACEDONIAN_LANGUAGE_CODE = 'mk';

    const PORTUGUESE_LANGUAGE_CODE = 'pt';

    const GREEKLISH_LANGUAGE_CODE = 'el__greeklish';

    const GREEK_LANGUAGE_CODE = 'el';

    const HINDI_LANGUAGE_CODE = 'hi';

    const SWEDISH_LANGUAGE_CODE = 'sv';

    const TURKISH_LANGUAGE_CODE = 'tr';

    const BULGARIAN_LANGUAGE_CODE = 'bg';

    const HUNGARIAN_LANGUAGE_CODE = 'hu';

    const MYANMAR_LANGUAGE_CODE = 'my';

    const CROATIAN_LANGUAGE_CODE = 'hr';

    const FINNISH_LANGUAGE_CODE = 'fi';

    const GEORGIAN_LANGUAGE_CODE = 'ka';

    const RUSSIAN_LANGUAGE_CODE = 'ru';

    const RUSSIAN_PASSPORT_2013_LANGUAGE_CODE = 'ru__passport_2013';

    const RUSSIAN_GOST_2000_B_LANGUAGE_CODE = 'ru__gost_2000_b';

    const UKRAINIAN_LANGUAGE_CODE = 'uk';

    const KAZAKH_LANGUAGE_CODE = 'kk';

    const CZECH_LANGUAGE_CODE = 'cs';

    const DANISH_LANGUAGE_CODE = 'da';

    const POLISH_LANGUAGE_CODE = 'pl';

    const ROMANIAN_LANGUAGE_CODE = 'ro';

    const ESPERANTO_LANGUAGE_CODE = 'eo';

    const ESTONIAN_LANGUAGE_CODE = 'et';

    const LATVIAN_LANGUAGE_CODE = 'lv';

    const LITHUANIAN_LANGUAGE_CODE = 'lt';

    const NORWEGIAN_LANGUAGE_CODE = 'no';

    const VIETNAMESE_LANGUAGE_CODE = 'vi';

    const ARABIC_LANGUAGE_CODE = 'ar';

    const PERSIAN_LANGUAGE_CODE = 'fa';

    const SERBIAN_LANGUAGE_CODE = 'sr';

    const SERBIAN_CYRILLIC_LANGUAGE_CODE = 'sr__cyr';

    const SERBIAN_LATIN_LANGUAGE_CODE = 'sr__lat';

    const AZERBAIJANI_LANGUAGE_CODE = 'az';

    const SLOVAK_LANGUAGE_CODE = 'sk';

    const FRENCH_LANGUAGE_CODE = 'fr';

    const FRENCH_AUSTRIAN_LANGUAGE_CODE = 'fr_at';

    const FRENCH_SWITZERLAND_LANGUAGE_CODE = 'fr_ch';

    const GERMAN_LANGUAGE_CODE = 'de';

    const GERMAN_AUSTRIAN_LANGUAGE_CODE = 'de_at';

    const GERMAN_SWITZERLAND_LANGUAGE_CODE = 'de_ch';

    const ENGLISH_LANGUAGE_CODE = 'en';

    const EXTRA_LATIN_CHARS_LANGUAGE_CODE = 'latin';

    const EXTRA_WHITESPACE_CHARS_LANGUAGE_CODE = ' ';

    const EXTRA_MSWORD_CHARS_LANGUAGE_CODE = 'msword';

    /**
     * @var array<string, array<string, string>>|null
     */
    private static $ASCII_MAPS;

    /**
     * @var array<string, array<string, string>>|null
     */
    private static $ASCII_MAPS_AND_EXTRAS;

    /**
     * @var array<string, array<string, string>>|null
     */
    private static $ASCII_EXTRAS;

    /**
     * @var array<string, int>|null
     */
    private static $ORD;

    /**
     * url: https://en.wikipedia.org/wiki/Wikipedia:ASCII#ASCII_printable_characters
     *
     * @var string
     */
    private static $REGEX_ASCII = "[^\x09\x10\x13\x0A\x0D\x20-\x7E]";

    private const REGEX_PRINTABLE_ASCII = '[^\x20-\x7E]';

    /**
     * bidirectional text chars
     *
     * url: https://www.w3.org/International/questions/qa-bidi-unicode-controls
     *
     * @var array<int, string>
     */
    private static $BIDI_UNI_CODE_CONTROLS_TABLE = [
        // LEFT-TO-RIGHT EMBEDDING (use -> dir = "ltr")
        8234 => "\xE2\x80\xAA",
        // RIGHT-TO-LEFT EMBEDDING (use -> dir = "rtl")
        8235 => "\xE2\x80\xAB",
        // POP DIRECTIONAL FORMATTING // (use -> </bdo>)
        8236 => "\xE2\x80\xAC",
        // LEFT-TO-RIGHT OVERRIDE // (use -> <bdo dir = "ltr">)
        8237 => "\xE2\x80\xAD",
        // RIGHT-TO-LEFT OVERRIDE // (use -> <bdo dir = "rtl">)
        8238 => "\xE2\x80\xAE",
        // LEFT-TO-RIGHT ISOLATE // (use -> dir = "ltr")
        8294 => "\xE2\x81\xA6",
        // RIGHT-TO-LEFT ISOLATE // (use -> dir = "rtl")
        8295 => "\xE2\x81\xA7",
        // FIRST STRONG ISOLATE // (use -> dir = "auto")
        8296 => "\xE2\x81\xA8",
        // POP DIRECTIONAL ISOLATE
        8297 => "\xE2\x81\xA9",
    ];

    /**
     * Transliteration placeholders used by the generated data tables.
     *
     * @var array<string, true>
     */
    private const UNKNOWN_TRANSLITERATION_MARKERS = [
        '[?]' => true,
        '[?] ' => true,
    ];

    /**
     * Match exactly the structurally valid multibyte UTF-8 sequences defined by RFC 3629.
     *
     * The ranges mirror those used in clean() and deliberately exclude:
     *   - 0xC0/0xC1 lead bytes (overlong 2-byte sequences such as "\xC0\xAF" for "/")
     *   - \xE0 followed by \x80–\x9F (overlong 3-byte sequences)
     *   - \xED followed by \xA0–\xBF (UTF-16 surrogate halves U+D800–U+DFFF)
     *   - \xF0 followed by \x80–\x8F (overlong 4-byte sequences)
     *   - \xF4 followed by \x90–\xBF, and \xF5–\xFF (code points above U+10FFFF)
     *
     * Without this strictness the later ordinal arithmetic (ord – 192/224/240) would
     * silently decode overlong sequences into their ASCII equivalents (e.g. "\xC0\xAF" → "/"),
     * introducing an input-sanitization bypass even though clean() already strips them.
     *
     * @var string
     */
    private const UTF8_MULTIBYTE_SEQUENCE_RX = '/[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE-\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2}/';


    /**
     * Get all languages from the constants "ASCII::.*LANGUAGE_CODE".
     *
     * @return array<string, string>
     *                                 <p>An associative array where the key is the language code in lowercase
     *                                 and the value is the corresponding language string.</p>
     */
    public static function getAllLanguages(): array
    {
        // init
        static $LANGUAGES = [];

        if ($LANGUAGES !== []) {
            return $LANGUAGES;
        }

        foreach ((new \ReflectionClass(__CLASS__))->getConstants() as $constant => $lang) {
            if (\strpos($constant, 'EXTRA') !== false) {
                $LANGUAGES[\strtolower($constant)] = $lang;
            } else {
                $LANGUAGES[\strtolower(\str_replace('_LANGUAGE_CODE', '', $constant))] = $lang;
            }
        }

        return $LANGUAGES;
    }

    /**
     * Returns an replacement array for ASCII methods.
     *
     * EXAMPLE: <code>
     * $array = ASCII::charsArray();
     * var_dump($array['ru']['б']); // 'b'
     * </code>
     *
     * @param bool $replace_extra_symbols [optional] <p>Add some more replacements e.g. "£" with " pound ".</p>
     *
     * @psalm-pure
     *
     * @return array<string, array<string , string>>
     *                                               <p>An array where the key is the language code, and the value is
     *                                               an associative array mapping original characters to their replacements.</p>
     */
    public static function charsArray(bool $replace_extra_symbols = false): array
    {
        if ($replace_extra_symbols) {
            self::prepareAsciiAndExtrasMaps();

            return self::$ASCII_MAPS_AND_EXTRAS ?? [];
        }

        self::prepareAsciiMaps();

        return self::$ASCII_MAPS ?? [];
    }

    /**
     * Returns an replacement array for ASCII methods with a mix of multiple languages.
     *
     * EXAMPLE: <code>
     * $array = ASCII::charsArrayWithMultiLanguageValues();
     * var_dump($array['b']); // ['β', 'б', 'ဗ', 'ბ', 'ب']
     * </code>
     *
     * @param bool $replace_extra_symbols [optional] <p>Add some more replacements e.g. "£" with " pound ".</p>
     *
     * @psalm-pure
     *
     * @return array<string, list<string>>
     *                                     <p>An array of replacements.</p>
     */
    public static function charsArrayWithMultiLanguageValues(bool $replace_extra_symbols = false): array
    {
        static $CHARS_ARRAY = [];
        $cacheKey = '' . $replace_extra_symbols;

        if (isset($CHARS_ARRAY[$cacheKey])) {
            return $CHARS_ARRAY[$cacheKey];
        }

        // init
        $return = [];
        $language_all_chars = self::charsArrayWithSingleLanguageValues(
            $replace_extra_symbols,
            false
        );

        /* @noinspection AlterInForeachInspection | ok here */
        foreach ($language_all_chars as $key => &$value) {
            $return[$value][] = $key;
        }

        $CHARS_ARRAY[$cacheKey] = $return;

        return $return;
    }

    /**
     * Returns an replacement array for ASCII methods with one language.
     *
     * For example, German will map 'ä' to 'ae', while other languages
     * will simply return e.g. 'a'.
     *
     * EXAMPLE: <code>
     * $array = ASCII::charsArrayWithOneLanguage('ru');
     * $tmpKey = \array_search('yo', $array['replace']);
     * echo $array['orig'][$tmpKey]; // 'ё'
     * </code>
     *
     * @param string $language              [optional] <p>Language of the source string e.g.: en, de_at, or de-ch.
     *                                      (default is 'en') | ASCII::*_LANGUAGE_CODE</p>
     * @param bool   $replace_extra_symbols [optional] <p>Add some more replacements e.g. "£" with " pound ".</p>
     * @param bool   $asOrigReplaceArray    [optional] <p>TRUE === return {orig: list<string>, replace: list<string>}
     *                                      array</p>
     *
     * @psalm-pure
     *
     * @return ($asOrigReplaceArray is true ? array{orig: list<string>, replace: list<string>} : array<string, string>)
     *
     * @phpstan-param ASCII::*_LANGUAGE_CODE $language
     */
    public static function charsArrayWithOneLanguage(
        string $language = self::ENGLISH_LANGUAGE_CODE,
        bool $replace_extra_symbols = false,
        bool $asOrigReplaceArray = true
    ): array {
        $language = self::get_language($language);

        // init
        static $CHARS_ARRAY = [];
        $cacheKey = '' . $replace_extra_symbols . '-' . $asOrigReplaceArray;

        // check static cache
        if (isset($CHARS_ARRAY[$cacheKey][$language])) {
            return $CHARS_ARRAY[$cacheKey][$language];
        }

        if ($replace_extra_symbols) {
            self::prepareAsciiAndExtrasMaps();

            if (isset(self::$ASCII_MAPS_AND_EXTRAS[$language])) {
                $tmpArray = self::$ASCII_MAPS_AND_EXTRAS[$language];

                if ($asOrigReplaceArray) {
                    $CHARS_ARRAY[$cacheKey][$language] = [
                        'orig'    => \array_keys($tmpArray),
                        'replace' => \array_values($tmpArray),
                    ];
                } else {
                    $CHARS_ARRAY[$cacheKey][$language] = $tmpArray;
                }
            } else {
                if ($asOrigReplaceArray) {
                    $CHARS_ARRAY[$cacheKey][$language] = [
                        'orig'    => [],
                        'replace' => [],
                    ];
                } else {
                    $CHARS_ARRAY[$cacheKey][$language] = [];
                }
            }
        } else {
            self::prepareAsciiMaps();

            if (isset(self::$ASCII_MAPS[$language])) {
                $tmpArray = self::$ASCII_MAPS[$language];

                if ($asOrigReplaceArray) {
                    $CHARS_ARRAY[$cacheKey][$language] = [
                        'orig'    => \array_keys($tmpArray),
                        'replace' => \array_values($tmpArray),
                    ];
                } else {
                    $CHARS_ARRAY[$cacheKey][$language] = $tmpArray;
                }
            } else {
                if ($asOrigReplaceArray) {
                    $CHARS_ARRAY[$cacheKey][$language] = [
                        'orig'    => [],
                        'replace' => [],
                    ];
                } else {
                    $CHARS_ARRAY[$cacheKey][$language] = [];
                }
            }
        }

        return $CHARS_ARRAY[$cacheKey][$language] ?? ['orig' => [], 'replace' => []];
    }

    /**
     * Returns an replacement array for ASCII methods with multiple languages.
     *
     * EXAMPLE: <code>
     * $array = ASCII::charsArrayWithSingleLanguageValues();
     * $tmpKey = \array_search('hnaik', $array['replace']);
     * echo $array['orig'][$tmpKey]; // '၌'
     * </code>
     *
     * @param bool $replace_extra_symbols [optional] <p>Add some more replacements e.g. "£" with " pound ".</p>
     * @param bool $asOrigReplaceArray    [optional] <p>TRUE === return {orig: list<string>, replace: list<string>}
     *                                    array</p>
     *
     * @psalm-pure
     *
     * @return ($asOrigReplaceArray is true ? array{orig: list<string>, replace: list<string>} : array<string, string>)
     */
    public static function charsArrayWithSingleLanguageValues(
        bool $replace_extra_symbols = false,
        bool $asOrigReplaceArray = true
    ): array {
        // init
        static $CHARS_ARRAY = [];
        $cacheKey = '' . $replace_extra_symbols . '-' . $asOrigReplaceArray;

        if (isset($CHARS_ARRAY[$cacheKey])) {
            return $CHARS_ARRAY[$cacheKey];
        }

        if ($replace_extra_symbols) {
            self::prepareAsciiAndExtrasMaps();

            /* @noinspection AlterInForeachInspection | ok here */
            foreach (self::$ASCII_MAPS_AND_EXTRAS ?? [] as &$map) {
                $CHARS_ARRAY[$cacheKey][] = $map;
            }
        } else {
            self::prepareAsciiMaps();

            /* @noinspection AlterInForeachInspection | ok here */
            foreach (self::$ASCII_MAPS ?? [] as &$map) {
                $CHARS_ARRAY[$cacheKey][] = $map;
            }
        }

        $CHARS_ARRAY[$cacheKey] = \array_merge([], ...$CHARS_ARRAY[$cacheKey]);

        if ($asOrigReplaceArray) {
            $CHARS_ARRAY[$cacheKey] = [
                'orig'    => \array_keys($CHARS_ARRAY[$cacheKey]),
                'replace' => \array_values($CHARS_ARRAY[$cacheKey]),
            ];
        }

        return $CHARS_ARRAY[$cacheKey];
    }

    /**
     * Accepts a string and removes all non-UTF-8 characters from it + extras if needed.
     *
     * @param string $str                         <p>The string to be sanitized.</p>
     * @param bool   $normalize_whitespace        [optional] <p>Set to true, if you need to normalize the
     *                                            whitespace.</p>
     * @param bool   $normalize_msword            [optional] <p>Set to true, if you need to normalize MS Word chars
     *                                            e.g.: "…"
     *                                            => "..."</p>
     * @param bool   $keep_non_breaking_space     [optional] <p>Set to true, to keep non-breaking-spaces, in
     *                                            combination with
     *                                            $normalize_whitespace</p>
     * @param bool   $remove_invisible_characters [optional] <p>Set to false, if you not want to remove invisible
     *                                            characters e.g.: "\0"</p>
     * @param bool   $remove_invalid_utf8         [optional] <p>Set to true to discard malformed UTF-8 byte
     *                                            sequences before other normalization steps.</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>A clean UTF-8 string.</p>
     */
    public static function clean(
        string $str,
        bool $normalize_whitespace = true,
        bool $keep_non_breaking_space = false,
        bool $normalize_msword = true,
        bool $remove_invisible_characters = true,
        bool $remove_invalid_utf8 = true
    ): string {
        // http://stackoverflow.com/questions/1401317/remove-non-utf8-characters-from-string
        // caused connection reset problem on larger strings

        if ($remove_invalid_utf8) {
            $regex = '/
              (
                (?: [\x00-\x7F]                           # single-byte sequences   0xxxxxxx
                |   [\xC2-\xDF][\x80-\xBF]                # double-byte sequences   110xxxxx 10xxxxxx
                |   \xE0[\xA0-\xBF][\x80-\xBF]            # triple-byte sequences   excluding overlongs
                |   [\xE1-\xEC\xEE-\xEF][\x80-\xBF]{2}    # triple-byte sequences   excluding surrogates
                |   \xED[\x80-\x9F][\x80-\xBF]            # triple-byte sequences   excluding surrogates
                |   \xF0[\x90-\xBF][\x80-\xBF]{2}         # quadruple-byte sequences excluding overlongs
                |   [\xF1-\xF3][\x80-\xBF]{3}             # quadruple-byte sequences
                |   \xF4[\x80-\x8F][\x80-\xBF]{2}         # quadruple-byte sequences up to U+10FFFF
                ){1,100}                                  # ...one or more times
              )
            | ( [\x80-\xBF] )                             # invalid byte in range 10000000 - 10111111
            | ( [\xC0-\xFF] )                             # invalid byte in range 11000000 - 11111111
            /x';
            $str = (string) \preg_replace($regex, '$1', $str);
        }

        if ($normalize_whitespace) {
            $str = self::normalize_whitespace($str, $keep_non_breaking_space);
        }

        if ($normalize_msword) {
            $str = self::normalize_msword($str);
        }

        if ($remove_invisible_characters) {
            $str = self::remove_invisible_characters($str);
        }

        return $str;
    }

    /**
     * Checks if a string is 7-bit ASCII.
     *
     * EXAMPLE: <code>
     * ASCII::is_ascii('白'); // false
     * </code>
     *
     * @param string $str <p>The string to check.</p>
     *
     * @psalm-pure
     *
     * @return bool
     *              <p>
     *              <strong>true</strong> if it is ASCII<br>
     *              <strong>false</strong> otherwise
     *              </p>
     */
    public static function is_ascii(string $str): bool
    {
        if ($str === '') {
            return true;
        }

        return !\preg_match('/' . self::$REGEX_ASCII . '/', $str);
    }

    /**
     * Returns a string with smart quotes, ellipsis characters, and dashes from
     * Windows-1252 (commonly used in Word documents) replaced by their ASCII
     * equivalents.
     *
     * EXAMPLE: <code>
     * ASCII::normalize_msword('„Abcdef…”'); // '"Abcdef..."'
     * </code>
     *
     * @param string $str <p>The string to be normalized.</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>A string with normalized characters for commonly used chars in Word documents.</p>
     */
    public static function normalize_msword(string $str): string
    {
        if ($str === '') {
            return '';
        }

        static $MSWORD_CACHE = ['orig' => [], 'replace' => []];

        if (empty($MSWORD_CACHE['orig'])) {
            self::prepareAsciiMaps();

            $map = self::$ASCII_MAPS[self::EXTRA_MSWORD_CHARS_LANGUAGE_CODE] ?? [];

            $MSWORD_CACHE = [
                'orig'    => \array_keys($map),
                'replace' => \array_values($map),
            ];
        }

        return \str_replace($MSWORD_CACHE['orig'], $MSWORD_CACHE['replace'], $str);
    }

    /**
     * Normalize the whitespace.
     *
     * EXAMPLE: <code>
     * ASCII::normalize_whitespace("abc-\xc2\xa0-öäü-\xe2\x80\xaf-\xE2\x80\xAC", true); // "abc-\xc2\xa0-öäü- -"
     * </code>
     *
     * @param string $str                          <p>The string to be normalized.</p>
     * @param bool   $keepNonBreakingSpace         [optional] <p>Set to true, to keep non-breaking-spaces.</p>
     * @param bool   $keepBidiUnicodeControls      [optional] <p>Set to true, to keep non-printable (for the web)
     *                                             bidirectional text chars.</p>
     * @param bool   $normalize_control_characters [optional] <p>Set to true, to convert e.g. LINE-, PARAGRAPH-SEPARATOR with "\n" and LINE TABULATION with "\t".</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>A string with normalized whitespace.</p>
     */
    public static function normalize_whitespace(
        string $str,
        bool $keepNonBreakingSpace = false,
        bool $keepBidiUnicodeControls = false,
        bool $normalize_control_characters = false
    ): string {
        if ($str === '') {
            return '';
        }

        static $WHITESPACE_CACHE = [];
        $cacheKey = (int) $keepNonBreakingSpace;

        if ($normalize_control_characters) {
            $str = \str_replace(
                [
                    "\x0d\x0c",     // 'END OF LINE'
                    "\xe2\x80\xa8", // 'LINE SEPARATOR'
                    "\xe2\x80\xa9", // 'PARAGRAPH SEPARATOR'
                    "\x0c",         // 'FORM FEED' // "\f"
                    "\x0b",         // 'VERTICAL TAB' // "\v"
                ],
                [
                    "\n",
                    "\n",
                    "\n",
                    "\n",
                    "\t",
                ],
                $str
            );
        }

        if (!isset($WHITESPACE_CACHE[$cacheKey])) {
            self::prepareAsciiMaps();

            $WHITESPACE_CACHE[$cacheKey] = self::$ASCII_MAPS[self::EXTRA_WHITESPACE_CHARS_LANGUAGE_CODE] ?? [];

            if ($keepNonBreakingSpace) {
                unset($WHITESPACE_CACHE[$cacheKey]["\xc2\xa0"]);
            }

            $WHITESPACE_CACHE[$cacheKey] = array_keys($WHITESPACE_CACHE[$cacheKey]);
        }

        if (!$keepBidiUnicodeControls) {
            static $BIDI_UNICODE_CONTROLS_CACHE = null;

            if ($BIDI_UNICODE_CONTROLS_CACHE === null) {
                $BIDI_UNICODE_CONTROLS_CACHE = self::$BIDI_UNI_CODE_CONTROLS_TABLE;
            }

            $str = \str_replace($BIDI_UNICODE_CONTROLS_CACHE, '', $str);
        }

        return \str_replace($WHITESPACE_CACHE[$cacheKey], ' ', $str);
    }

    /**
     * Remove invisible characters from a string.
     *
     * This prevents malicious code injection through null bytes or other control characters.
     *
     * copy&past from https://github.com/bcit-ci/CodeIgniter/blob/develop/system/core/Common.php
     *
     * @param string $str
     * @param bool   $url_encoded
     * @param string $replacement
     * @param bool   $keep_basic_control_characters
     *
     * @psalm-pure
     *
     * @return string
     */
    public static function remove_invisible_characters(
        string $str,
        bool $url_encoded = false,
        string $replacement = '',
        bool $keep_basic_control_characters = true
    ): string {
        // init
        $non_displayables = [];

        // every control character except:
        // - newline (dec 10),
        // - carriage return (dec 13),
        // - horizontal tab (dec 09)
        if ($url_encoded) {
            $non_displayables[] = '/%0[0-8bcefBCEF]/'; // url encoded 00-08, 11, 12, 14, 15
            $non_displayables[] = '/%1[0-9a-fA-F]/'; // url encoded 16-31
        }

        if ($keep_basic_control_characters) {
            $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S'; // 00-08, 11, 12, 14-31, 127
        } else {
            $str = self::normalize_whitespace($str, false, false, true);
            $non_displayables[] = '/[^\P{C}\s]/u';
        }

        do {
            $str = (string) \preg_replace($non_displayables, $replacement, $str, -1, $count);
        } while ($count !== 0);

        return $str;
    }

    /**
     * WARNING: This method will return broken characters and is only for special cases.
     *
     * Convert two UTF-8 encoded strings to a single-byte strings suitable for
     * functions that need the same string length after the conversion.
     *
     * The function simply uses (and updates) a tailored dynamic encoding
     * (in/out map parameter) where non-ascii characters are remapped to
     * the range [128-255] in order of appearance.
     *
     * @return array{0: string, 1: string}
     */
    public static function to_ascii_remap(string $str1, string $str2): array
    {
        $charMap = [];
        $str1 = self::to_ascii_remap_intern($str1, $charMap);
        $str2 = self::to_ascii_remap_intern($str2, $charMap);

        return [$str1, $str2];
    }

    /**
     * Returns an ASCII version of the string. A set of non-ASCII characters are
     * replaced with their closest ASCII counterparts, and the rest are removed
     * by default. The language or locale of the source string can be supplied
     * for language-specific transliteration in any of the following formats:
     * en, en_GB, or en-GB. For example, passing "de" results in "äöü" mapping
     * to "aeoeue" rather than "aou" as in other languages.
     *
     * EXAMPLE: <code>
     * ASCII::to_ascii('�Düsseldorf�', 'en'); // Dusseldorf
     * </code>
     *
     * @param string    $str                       <p>The input string.</p>
     * @param string    $language                  [optional] <p>Language of the source string.
     *                                             (default is 'en') | ASCII::*_LANGUAGE_CODE</p>
     * @param bool      $remove_unsupported_chars  [optional] <p>Whether to remove the
     *                                             unsupported characters.</p>
     * @param bool      $replace_extra_symbols     [optional]  <p>Add some more replacements e.g. "£" with " pound
     *                                             ".</p>
     * @param bool      $use_transliterate         [optional]  <p>Use ASCII::to_transliterate() for unknown chars.</p>
     * @param bool      $replace_single_chars_only [optional]  <p>Single char replacement is better for the
     *                                             performance, but some languages need to replace more than one char
     *                                             at the same time. If FALSE === auto-setting, depended on the
     *                                             language</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>A string that contains only ASCII characters.</p>
     *
     * @phpstan-param ASCII::*_LANGUAGE_CODE $language
     */
    public static function to_ascii(
        string $str,
        string $language = self::ENGLISH_LANGUAGE_CODE,
        bool $remove_unsupported_chars = true,
        bool $replace_extra_symbols = false,
        bool $use_transliterate = false,
        bool $replace_single_chars_only = false
    ): string {
        if ($str === '') {
            return '';
        }

        // fast path: pure printable ASCII — single regex covers is_ascii + no-control-chars
        if (
            !$replace_extra_symbols
            &&
            !\preg_match('/' . self::REGEX_PRINTABLE_ASCII . '/', $str)
        ) {
            return $str;
        }

        $language = self::get_language($language);
        /** @phpstan-var ASCII::*_LANGUAGE_CODE $language - hack for phpstan */

        if (
            !$replace_extra_symbols
            &&
            \strlen($str) <= 64
        ) {
            $isValidUtf8 = true;
            $str = self::to_ascii_replace($str, $language, $replace_extra_symbols, $replace_single_chars_only, $isValidUtf8);

            if ($isValidUtf8) {
                self::prepareAsciiMaps();
                if (!isset(self::$ASCII_MAPS[$language])) {
                    $use_transliterate = true;
                }

                if ($use_transliterate) {
                    $str = self::to_transliterate($str, null, false);
                }

                if ($remove_unsupported_chars) {
                    if (!\preg_match('/' . self::REGEX_PRINTABLE_ASCII . '/', $str)) {
                        return $str;
                    }

                    $str = (string) \str_replace(["\r\n", "\n", "\r", "\t"], ' ', $str);
                    $str = (string) \preg_replace('/' . self::$REGEX_ASCII . '/', '', $str);
                }

                return $str;
            }
        }

        // secondary fast path: only 7-bit bytes (no multibyte UTF-8).
        // Strings with control chars (\x00-\x1F, \x7F) but no high bytes
        // still need $remove_unsupported_chars cleanup, but never need the
        // strtr replacement map because all replaceable characters are ≥ 0x80.
        if (
            !$replace_extra_symbols
            &&
            !\preg_match('/[\x80-\xFF]/', $str)
        ) {
            if ($remove_unsupported_chars) {
                $str = (string) \str_replace(["\r\n", "\n", "\r", "\t"], ' ', $str);
                $str = (string) \preg_replace('/' . self::$REGEX_ASCII . '/', '', $str);
            }

            return $str;
        }

        // invalid UTF-8: apply replacement map first, then clean up.
        // strtr() can match partial byte sequences from malformed UTF-8 against
        // valid lookup keys, producing incorrect output, so we must fall back to
        // the clean-then-transliterate path for invalid input.
        if (\preg_match('//u', $str) !== 1) {
            self::prepareAsciiMaps();

            if (!isset(self::$ASCII_MAPS[$language])) {
                $use_transliterate = true;
            }

            if ($use_transliterate) {
                $str = self::to_transliterate($str, null, false);
            }

            if ($remove_unsupported_chars) {
                if (!\preg_match('/' . self::REGEX_PRINTABLE_ASCII . '/', $str)) {
                    return $str;
                }

                $str = (string) \str_replace(["\r\n", "\n", "\r", "\t"], ' ', $str);
                $str = (string) \preg_replace('/' . self::$REGEX_ASCII . '/', '', $str);
            }

            return $str;
        }

        self::prepareAsciiMaps();
        if (!isset(self::$ASCII_MAPS[$language])) {
            $use_transliterate = true;
        }

        // For English transliteration mode, going directly through
        // to_transliterate() avoids an expensive replacement-map pass that
        // rarely contributes useful substitutions for non-Latin long strings.
        if (
            $use_transliterate
            &&
            !$replace_extra_symbols
            &&
            !$replace_single_chars_only
            &&
            $language === self::ENGLISH_LANGUAGE_CODE
        ) {
            $str = self::to_transliterate($str, null, false);
        } else {
            // Apply the ASCII replacement map via strtr().
            $str = self::to_ascii_replace($str, $language, $replace_extra_symbols, $replace_single_chars_only);

            if ($use_transliterate) {
                $str = self::to_transliterate($str, null, false);
            }
        }

        if ($remove_unsupported_chars) {
            if (!\preg_match('/' . self::REGEX_PRINTABLE_ASCII . '/', $str)) {
                return $str;
            }

            $str = (string) \str_replace(["\r\n", "\n", "\r", "\t"], ' ', $str);
            $str = (string) \preg_replace('/' . self::$REGEX_ASCII . '/', '', $str);
        }

        return $str;
    }

    /**
     * Convert given string to safe filename (and keep string case).
     *
     * EXAMPLE: <code>
     * ASCII::to_filename('שדגשדג.png', true)); // 'shdgshdg.png'
     * </code>
     *
     * @param string $str               <p>The string input.</p>
     * @param bool   $use_transliterate <p>ASCII::to_transliterate() is used by default - unsafe characters are
     *                                  simply replaced with hyphen otherwise.</p>
     * @param string $fallback_char     <p>The fallback character. - "-" is the default</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>A string that contains only safe characters for a filename.</p>
     */
    public static function to_filename(
        string $str,
        bool $use_transliterate = true,
        string $fallback_char = '-'
    ): string {
        if ($use_transliterate) {
            $str = self::to_transliterate($str, $fallback_char);
        }

        $fallback_char_escaped = \preg_quote($fallback_char, '/');

        $str = (string) \preg_replace(
            [
                '/[^' . $fallback_char_escaped . '.\\-a-zA-Z\d\\s]/', // 1) remove un-needed chars
                '/\s+/u',                                             // 2) convert spaces to $fallback_char
                '/[' . $fallback_char_escaped . ']+/u',               // 3) remove double $fallback_char's
            ],
            [
                '',
                $fallback_char,
                $fallback_char,
            ],
            $str
        );

        return \trim($str, $fallback_char);
    }

    /**
     * Converts a string into a URL-friendly slug.
     *
     * - This includes replacing non-ASCII characters with their closest ASCII equivalents, removing remaining
     *   non-ASCII and non-alphanumeric characters, and replacing whitespace with $separator.
     * - The separator defaults to a single dash, and the string is also converted to lowercase.
     * - The language of the source string can also be supplied for language-specific transliteration.
     *
     * @param string                $str                   <p>The string input.</p>
     * @param string                $separator             [optional] <p>The string used to replace whitespace.</p>
     * @param string                $language              [optional] <p>Language of the source string.
     *                                                     (default is 'en') | ASCII::*_LANGUAGE_CODE</p>
     * @param array<string, string> $replacements          [optional] <p>A map of replaceable strings.</p>
     * @param bool                  $replace_extra_symbols [optional] <p>Add some more replacements e.g. "£" with "
     *                                                     pound ".</p>
     * @param bool                  $use_str_to_lower      [optional] <p>Use "string to lower" for the input.</p>
     * @param bool                  $use_transliterate     [optional] <p>Use ASCII::to_transliterate() for unknown
     *                                                     chars.</p>
     * @psalm-pure
     *
     * @return string
     *                <p>The URL-friendly slug.</p>
     *
     * @phpstan-param ASCII::*_LANGUAGE_CODE $language
     */
    public static function to_slugify(
        string $str,
        string $separator = '-',
        string $language = self::ENGLISH_LANGUAGE_CODE,
        array $replacements = [],
        bool $replace_extra_symbols = false,
        bool $use_str_to_lower = true,
        bool $use_transliterate = false
    ): string {
        if ($str === '') {
            return '';
        }

        foreach ($replacements as $from => $to) {
            $str = \str_replace($from, $to, $str);
        }

        if (
            !$replace_extra_symbols
            &&
            !$use_transliterate
            &&
            self::get_language($language) === self::ENGLISH_LANGUAGE_CODE
            &&
            !\preg_match('/' . self::REGEX_PRINTABLE_ASCII . '/', $str)
        ) {
            // Pure printable ASCII does not need transliteration or remapping.
        } else {
            $str = self::to_ascii(
                $str,
                $language,
                false,
                $replace_extra_symbols,
                $use_transliterate
            );
        }

        $str = \str_replace('@', $separator, $str);

        if ($use_str_to_lower) {
            $str = \strtolower($str);
            $str = (string) \preg_replace(
                '/[^a-z\\d\\s\\-_' . \preg_quote($separator, '/') . ']/',
                '',
                $str
            );
        } else {
            $str = (string) \preg_replace(
                '/[^a-zA-Z\\d\\s\\-_' . \preg_quote($separator, '/') . ']/',
                '',
                $str
            );
            $str = (string) \preg_replace('/\\B([A-Z])/', '-\1', $str);
        }

        $str = (string) \preg_replace('/^[\'\\s]+|[\'\\s]+$/', '', $str);
        $str = (string) \preg_replace('/[\\-_\\s]+/', $separator, $str);

        $l = \strlen($separator);
        if ($l && \strpos($str, $separator) === 0) {
            $str = (string) \substr($str, $l);
        }

        if (\substr($str, -$l) === $separator) {
            $str = (string) \substr($str, 0, \strlen($str) - $l);
        }

        return $str;
    }

    /**
     * Returns an ASCII version of the string. A set of non-ASCII characters are
     * replaced with their closest ASCII counterparts, and the rest are removed
     * unless instructed otherwise.
     *
     * EXAMPLE: <code>
     * ASCII::to_transliterate('déjà σσς iıii'); // 'deja sss iiii'
     * </code>
     *
     * @param string      $str     <p>The input string.</p>
     * @param string|null $unknown [optional] <p>Character use if character unknown. (default is '?')
     *                             But you can also use NULL to keep the unknown chars.</p>
     * @param bool        $strict  [optional] <p>Use "transliterator_transliterate()" from PHP-Intl
     *
     * @psalm-pure
     *
     * @return string
     *                <p>A String that contains only ASCII characters.</p>
     */
    public static function to_transliterate(
        string $str,
        $unknown = '?',
        bool $strict = false
    ): string {
        static $UTF8_TO_TRANSLIT = null;

        static $TRANSLITERATOR = null;

        static $SUPPORT_INTL = null;

        /** @var array<string, string|false> */
        static $TRANSLIT_CHAR_CACHE = [];
        /** @var array<string, array<string, string>> */
        static $WARM_MAPS = [];

        if ($str === '') {
            return '';
        }

        // Long pure printable ASCII strings are common in benchmarks and can
        // skip the broader ASCII/control-character validator entirely.
        if (
            isset($str[63])
            &&
            !\preg_match('/' . self::REGEX_PRINTABLE_ASCII . '/', $str)
        ) {
            return $str;
        }

        // check if we only have ASCII, first (better performance)
        if (\preg_match('/' . self::$REGEX_ASCII . '/', $str) === 0) {
            return $str;
        }

        // Prefix the cache key so unknown=null does not collide with an
        // explicit fallback string such as "\x00".
        $unknownCacheKey = $unknown === null
            ? "\x00null"
            : "\x01" . $unknown;

        if ($SUPPORT_INTL === null) {
            $SUPPORT_INTL = \extension_loaded('intl');
        }

        $warmPathAlreadyApplied = false;
        if (
            $unknown !== '?'
            &&
            isset($WARM_MAPS[$unknownCacheKey])
            &&
            \preg_match('//u', $str) === 1
        ) {
            $warmStr = \strtr($str, $WARM_MAPS[$unknownCacheKey]);
            if (!\preg_match('/[\x80-\xFF\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $warmStr)) {
                return $warmStr;
            }

            $str = $warmStr;
            $warmPathAlreadyApplied = true;
        }

        // only run the heavy clean() regex when the string has invalid UTF-8
        if (\preg_match('//u', $str) === 1) {
            if (
                $unknown === '?'
                ||
                \strpos($str, "\xC2") !== false
                ||
                \strpos($str, "\xE2") !== false
                ||
                \preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $str) === 1
            ) {
                $str_before_clean = $str;
                $str = self::normalize_whitespace($str);
                $str = self::normalize_msword($str);
                $str = self::remove_invisible_characters($str);
                $str = self::clean(
                    $str,
                    true,
                    false,
                    true,
                    false
                );
                if (
                    $str !== $str_before_clean
                    &&
                    \preg_match('/' . self::$REGEX_ASCII . '/', $str) === 0
                ) {
                    return $str;
                }
            }
        } else {
            $str_before_clean = $str;
            $str = self::clean($str);
            if (
                $str !== $str_before_clean
                &&
                \preg_match('/' . self::$REGEX_ASCII . '/', $str) === 0
            ) {
                return $str;
            }
        }

        if (
            $strict
            &&
            $SUPPORT_INTL === true
        ) {
            if (!isset($TRANSLITERATOR)) {
                // INFO: see "*-Latin" rules via "transliterator_list_ids()"
                $TRANSLITERATOR = \transliterator_create('NFKC; [:Nonspacing Mark:] Remove; NFKC; Any-Latin; Latin-ASCII;');
            }

            // INFO: https://unicode.org/cldr/utility/character.jsp
            $str_tmp = \transliterator_transliterate($TRANSLITERATOR, $str);

            if ($str_tmp !== false) {
                if (
                    $str_tmp !== $str
                    &&
                    \preg_match('/' . self::$REGEX_ASCII . '/', $str_tmp) === 0
                ) {
                    return $str_tmp;
                }

                $str = $str_tmp;
            }
        }

        if (self::$ORD === null) {
            self::$ORD = self::getData('ascii_ord');
        }

        // Copy the memoized ORD table into a local non-null alias so the hot
        // callback can read it without repeated nullable static-property checks.
        /** @var array<string, int> $ordMap */
        $ordMap = self::$ORD;

        // warm path: if we already built a map for this $unknown value, try it first
        if (
            !$warmPathAlreadyApplied
            &&
            isset($WARM_MAPS[$unknownCacheKey])
        ) {
            $str = \strtr($str, $WARM_MAPS[$unknownCacheKey]);

            if (!\preg_match('/[\x80-\xFF]/', $str)) {
                return $str;
            }
        }

        // collect unique non-ASCII characters and build a strtr map
        if (\preg_match_all(self::UTF8_MULTIBYTE_SEQUENCE_RX, $str, $nonAsciiMatches)) {
            $charMap = [];
            $seen = [];

            foreach ($nonAsciiMatches[0] as $c) {
                if (isset($seen[$c])) {
                    continue;
                }
                $seen[$c] = true;

                if (!\array_key_exists($c, $TRANSLIT_CHAR_CACHE)) {
                    $ordC0 = $ordMap[$c[0]];
                    $ordC1 = $ordMap[$c[1]];

                    if ($ordC0 <= 223) {
                        $ord = ($ordC0 - 192) * 64 + ($ordC1 - 128);
                    } elseif ($ordC0 <= 239) {
                        $ord = ($ordC0 - 224) * 4096 + ($ordC1 - 128) * 64 + ($ordMap[$c[2]] - 128);
                    } else {
                        $ord = ($ordC0 - 240) * 262144 + ($ordC1 - 128) * 4096 + ($ordMap[$c[2]] - 128) * 64 + ($ordMap[$c[3]] - 128);
                    }

                    $bank = $ord >> 8;
                    if (!isset($UTF8_TO_TRANSLIT[$bank])) {
                        $UTF8_TO_TRANSLIT[$bank] = self::getDataIfExists(\sprintf('x%03x', $bank));
                    }

                    $bankPos = $ord & 255;

                    if (
                        isset($UTF8_TO_TRANSLIT[$bank][$bankPos])
                        &&
                        !isset(self::UNKNOWN_TRANSLITERATION_MARKERS[$UTF8_TO_TRANSLIT[$bank][$bankPos]])
                    ) {
                        $TRANSLIT_CHAR_CACHE[$c] = $UTF8_TO_TRANSLIT[$bank][$bankPos];
                    } else {
                        $TRANSLIT_CHAR_CACHE[$c] = false;
                    }
                }

                $cached = $TRANSLIT_CHAR_CACHE[$c];

                if ($cached === false) {
                    if ($unknown !== null) {
                        $charMap[$c] = $unknown;
                    }
                } elseif ($cached === '' && $unknown === null) {
                    // keep original char
                } else {
                    $charMap[$c] = $cached;
                }
            }

            // merge new entries into the warm map for future calls
            if ($charMap !== []) {
                if (isset($WARM_MAPS[$unknownCacheKey])) {
                    foreach ($charMap as $k => $v) {
                        $WARM_MAPS[$unknownCacheKey][$k] = $v;
                    }
                } else {
                    $WARM_MAPS[$unknownCacheKey] = $charMap;
                }

                return \strtr($str, $WARM_MAPS[$unknownCacheKey]);
            }
        }

        return $str;
    }

    /**
     * WARNING: This method will return broken characters and is only for special cases.
     *
     * Convert a UTF-8 encoded string to a single-byte string suitable for
     * functions that need the same string length after the conversion.
     *
     * The function simply uses (and updates) a tailored dynamic encoding
     * (in/out map parameter) where non-ascii characters are remapped to
     * the range [128-255] in order of appearance.
     *
     * Thus, it supports up to 128 different multibyte code points max over
     * the whole set of strings sharing this encoding.
     *
     * Source: https://github.com/KEINOS/mb_levenshtein
     *
     * @param string $str <p>UTF-8 string to be converted to extended ASCII.</p>
     * @param array  $map <p>Internal-Map of code points to ASCII characters.</p>
     *
     * @return string
     *                <p>Mapped broken string.</p>
     *
     * @phpstan-param array<string, string> $map
     */
    private static function to_ascii_remap_intern(string $str, array &$map): string
    {
        // find all utf-8 characters
        $matches = [];
        if (!\preg_match_all('/[\xC0-\xF7][\x80-\xBF]+/', $str, $matches)) {
            return $str; // plain ascii string
        }

        // update the encoding map with the characters not already met
        $mapCount = \count($map);
        foreach ($matches[0] as $mbc) {
            if (!isset($map[$mbc])) {
                $map[$mbc] = \chr(128 + $mapCount);
                ++$mapCount;
            }
        }

        // finally, remap non-ascii characters
        return \strtr($str, $map);
    }


    /**
     * Apply the cached ASCII replacement map to a string via strtr().
     *
     * For medium and long UTF-8 inputs, filtering the replacement table by bytes
     * present in the input avoids feeding the full replacement table to strtr()
     * without introducing per-input cache growth.
     *
     * @phpstan-param ASCII::*_LANGUAGE_CODE $language
     * @param-out bool $isValidUtf8
     */
    private static function to_ascii_replace(
        string $str,
        string $language,
        bool $replace_extra_symbols,
        bool $replace_single_chars_only,
        ?bool &$isValidUtf8 = null
    ): string {
        static $REPLACE_HELPER_CACHE = [];
        static $MAP_BY_FIRST_BYTE = [];
        static $SHORT_FILTERED_MAP_CACHE = [];
        static $SHORT_FILTERED_MAP_CACHE_QUEUE = [];
        $cacheKey = $language . '-' . (int) $replace_extra_symbols . '-' . (int) $replace_single_chars_only;

        if (!isset($REPLACE_HELPER_CACHE[$cacheKey])) {
            $langAll = self::getAsciiAllReplacementMap($replace_extra_symbols, $replace_single_chars_only);

            $langSpecific = self::getAsciiLanguageReplacementMap($language, $replace_extra_symbols, $replace_single_chars_only);

            if ($langSpecific === []) {
                $REPLACE_HELPER_CACHE[$cacheKey] = $langAll;
            } else {
                $REPLACE_HELPER_CACHE[$cacheKey] = \array_merge([], $langAll, $langSpecific);
            }

            // Pre-index by first byte so long-string calls can cheaply skip most of
            // the replacement table instead of feeding the full language map to strtr().
            $MAP_BY_FIRST_BYTE[$cacheKey] = [];
            foreach ($REPLACE_HELPER_CACHE[$cacheKey] as $key => $val) {
                $MAP_BY_FIRST_BYTE[$cacheKey][$key[0]][$key] = $val;
            }
        }

        if (
            !$replace_extra_symbols
            &&
            \strlen($str) <= 64
        ) {
            $matchResult = \preg_match_all('/' . self::REGEX_PRINTABLE_ASCII . '/u', $str, $matches);
            if ($matchResult === false) {
                $isValidUtf8 = false;

                return $str;
            }

            $isValidUtf8 = true;

            if (!$matchResult) {
                return $str;
            }

            $cache = $REPLACE_HELPER_CACHE[$cacheKey];
            $chars = $matches[0];
            $charCount = \count($chars);

            if ($charCount === 1 && isset($cache[$chars[0]])) {
                return \str_replace($chars[0], $cache[$chars[0]], $str);
            }

            $shortCacheKey = $cacheKey . ':' . \implode('|', $chars);

            if (isset($SHORT_FILTERED_MAP_CACHE[$shortCacheKey])) {
                return \strtr($str, $SHORT_FILTERED_MAP_CACHE[$shortCacheKey]);
            }

            $filteredMap = [];

            if (
                !$replace_single_chars_only
                &&
                $charCount >= 2
            ) {
                // Mixed keys like "A̧" (ASCII + combining mark) are rare; let
                // strtr handle those with the full map to preserve correctness.
                if (\preg_match('/[A-Za-z][\x{0300}-\x{036F}]/u', $str) === 1) {
                    return \strtr($str, $cache);
                }

                for ($span = 5; $span >= 2; --$span) {
                    if ($charCount < $span) {
                        continue;
                    }

                    $lastIndex = $charCount - $span;
                    for ($idx = 0; $idx <= $lastIndex; ++$idx) {
                        $candidate = '';
                        for ($offset = 0; $offset < $span; ++$offset) {
                            $candidate .= $chars[$idx + $offset];
                        }

                        if (isset($cache[$candidate])) {
                            $filteredMap[$candidate] = $cache[$candidate];
                        }
                    }
                }
            }

            foreach ($chars as $char) {
                if (isset($cache[$char])) {
                    $filteredMap[$char] = $cache[$char];
                }
            }

            if ($filteredMap !== []) {
                $SHORT_FILTERED_MAP_CACHE[$shortCacheKey] = $filteredMap;
                $SHORT_FILTERED_MAP_CACHE_QUEUE[] = $shortCacheKey;
                if (\count($SHORT_FILTERED_MAP_CACHE_QUEUE) > 256) {
                    $oldestKey = \array_shift($SHORT_FILTERED_MAP_CACHE_QUEUE);
                    if ($oldestKey !== null) {
                        unset($SHORT_FILTERED_MAP_CACHE[$oldestKey]);
                    }
                }

                return \strtr($str, $filteredMap);
            }

            return $str;
        }

        $isValidUtf8 = true;

        // Build a filtered map containing only entries whose
        // leading byte is present in this specific input string.
        $indexedMap = &$MAP_BY_FIRST_BYTE[$cacheKey];
        $filteredMap = [];
        foreach (\count_chars($str, 1) as $byte => $count) {
            $fb = \chr($byte);
            if (isset($indexedMap[$fb])) {
                foreach ($indexedMap[$fb] as $k => $v) {
                    $filteredMap[$k] = $v;
                }
            }
        }

        if ($filteredMap !== []) {
            $str = \strtr($str, $filteredMap);
        }

        return $str;
    }

    /**
     * Get the language from a string.
     *
     * e.g.: de_at -> de_at
     *       de_DE -> de
     *       DE_DE -> de
     *       de-de -> de
     *
     * @return string
     */
    private static function get_language(string $language)
    {
        if ($language === '') {
            return '';
        }

        static $LANGUAGE_CACHE = [];
        if (isset($LANGUAGE_CACHE[$language])) {
            return $LANGUAGE_CACHE[$language];
        }

        if (
            \strpos($language, '_') === false
            &&
            \strpos($language, '-') === false
        ) {
            return $LANGUAGE_CACHE[$language] = \strtolower($language);
        }

        $language_tmp = \str_replace('-', '_', \strtolower($language));

        $regex = '/(?<first>[a-z]+)_\g{first}/';

        return $LANGUAGE_CACHE[$language] = (string) \preg_replace($regex, '$1', $language_tmp);
    }

    /**
     * @return array<string, string>
     */
    private static function getAsciiAllReplacementMap(
        bool $replace_extra_symbols,
        bool $replace_single_chars_only
    ): array {
        static $CACHE = [];
        $cacheKey = (int) $replace_extra_symbols . '-' . (int) $replace_single_chars_only;

        if (isset($CACHE[$cacheKey])) {
            return $CACHE[$cacheKey];
        }

        $CACHE[$cacheKey] = self::filterAsciiReplacementMap(
            self::charsArrayWithSingleLanguageValues($replace_extra_symbols, false),
            $replace_single_chars_only
        );

        return $CACHE[$cacheKey];
    }

    /**
     * @phpstan-param ASCII::*_LANGUAGE_CODE $language
     *
     * @return array<string, string>
     */
    private static function getAsciiLanguageReplacementMap(
        string $language,
        bool $replace_extra_symbols,
        bool $replace_single_chars_only
    ): array {
        static $CACHE = [];
        $cacheKey = $language . '-' . (int) $replace_extra_symbols . '-' . (int) $replace_single_chars_only;

        if (isset($CACHE[$cacheKey])) {
            return $CACHE[$cacheKey];
        }

        $CACHE[$cacheKey] = self::filterAsciiReplacementMap(
            self::charsArrayWithOneLanguage($language, $replace_extra_symbols, false),
            $replace_single_chars_only
        );

        return $CACHE[$cacheKey];
    }

    /**
     * Get data from "/data/*.php".
     *
     * @return array<array-key,mixed>
     */
    private static function getData(string $file)
    {
        return include __DIR__ . '/data/' . $file . '.php';
    }

    /**
     * Get data from "/data/*.php".
     *
     * @return array<array-key,mixed>
     */
    private static function getDataIfExists(string $file): array
    {
        $file = __DIR__ . '/data/' . $file . '.php';
        if (\is_file($file)) {
            return include $file;
        }

        return [];
    }

    /**
     * @param array<string, string> $map
     *
     * @return array<string, string>
     */
    private static function filterAsciiReplacementMap(array $map, bool $replace_single_chars_only): array
    {
        if ($replace_single_chars_only === false) {
            return $map;
        }

        foreach ($map as $char => $replacement) {
            // Single UTF-8 code points are at most 4 bytes, so 5+ bytes
            // can be rejected without the regex check.
            if (
                isset($char[4])
                ||
                \preg_match('/^.$/us', $char) !== 1
            ) {
                unset($map[$char]);
            }
        }

        return $map;
    }

    /**
     * @return void
     */
    private static function prepareAsciiAndExtrasMaps()
    {
        if (self::$ASCII_MAPS_AND_EXTRAS === null) {
            self::prepareAsciiMaps();
            self::prepareAsciiExtras();

            self::$ASCII_MAPS_AND_EXTRAS = \array_merge_recursive(
                self::$ASCII_MAPS ?? [],
                self::$ASCII_EXTRAS ?? []
            );
        }
    }

    /**
     * @return void
     */
    private static function prepareAsciiMaps()
    {
        if (self::$ASCII_MAPS === null) {
            self::$ASCII_MAPS = self::getData('ascii_by_languages');
        }
    }

    /**
     * @return void
     */
    private static function prepareAsciiExtras()
    {
        if (self::$ASCII_EXTRAS === null) {
            self::$ASCII_EXTRAS = self::getData('ascii_extras_by_languages');
        }
    }
}
