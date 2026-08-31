<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com> and Trevor Rowbotham <trevor.rowbotham@pm.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Polyfill\Intl\Idn;

use Symfony\Polyfill\Intl\Idn\Resources\unidata\DisallowedRanges;
use Symfony\Polyfill\Intl\Idn\Resources\unidata\Regex;

/**
 * @see https://www.unicode.org/reports/tr46/
 *
 * @internal
 */
final class Idn
{
    public const ERROR_EMPTY_LABEL = 1;
    public const ERROR_LABEL_TOO_LONG = 2;
    public const ERROR_DOMAIN_NAME_TOO_LONG = 4;
    public const ERROR_LEADING_HYPHEN = 8;
    public const ERROR_TRAILING_HYPHEN = 0x10;
    public const ERROR_HYPHEN_3_4 = 0x20;
    public const ERROR_LEADING_COMBINING_MARK = 0x40;
    public const ERROR_DISALLOWED = 0x80;
    public const ERROR_PUNYCODE = 0x100;
    public const ERROR_LABEL_HAS_DOT = 0x200;
    public const ERROR_INVALID_ACE_LABEL = 0x400;
    public const ERROR_BIDI = 0x800;
    public const ERROR_CONTEXTJ = 0x1000;
    public const ERROR_CONTEXTO_PUNCTUATION = 0x2000;
    public const ERROR_CONTEXTO_DIGITS = 0x4000;

    public const INTL_IDNA_VARIANT_2003 = 0;
    public const INTL_IDNA_VARIANT_UTS46 = 1;

    public const IDNA_DEFAULT = 0;
    public const IDNA_ALLOW_UNASSIGNED = 1;
    public const IDNA_USE_STD3_RULES = 2;
    public const IDNA_CHECK_BIDI = 4;
    public const IDNA_CHECK_CONTEXTJ = 8;
    public const IDNA_NONTRANSITIONAL_TO_ASCII = 16;
    public const IDNA_NONTRANSITIONAL_TO_UNICODE = 32;

    public const MAX_DOMAIN_SIZE = 253;
    public const MAX_LABEL_SIZE = 63;

    public const BASE = 36;
    public const TMIN = 1;
    public const TMAX = 26;
    public const SKEW = 38;
    public const DAMP = 700;
    public const INITIAL_BIAS = 72;
    public const INITIAL_N = 128;
    public const DELIMITER = '-';
    public const MAX_INT = 2147483647;

    /**
     * Contains the numeric value of a basic code point (for use in representing integers) in the
     * range 0 to BASE-1, or -1 if b is does not represent a value.
     *
     * @var array<int, int>
     */
    private static $basicToDigit = [
        -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
        -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,

        -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
        26, 27, 28, 29, 30, 31, 32, 33, 34, 35, -1, -1, -1, -1, -1, -1,

        -1,  0,  1,  2,  3,  4,  5,  6,  7,  8,  9, 10, 11, 12, 13, 14,
        15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, -1, -1, -1, -1, -1,

        -1,  0,  1,  2,  3,  4,  5,  6,  7,  8,  9, 10, 11, 12, 13, 14,
        15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, -1, -1, -1, -1, -1,

        -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
        -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,

        -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
        -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,

        -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
        -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,

        -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
        -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
    ];

    /**
     * @var array<int, int>
     */
    private static $virama;

    /**
     * @var array<int, string>
     */
    private static $mapped;

    /**
     * @var array<int, bool>
     */
    private static $ignored;

    /**
     * @var array<int, string>
     */
    private static $deviation;

    /**
     * @var array<int, bool>
     */
    private static $disallowed;

    /**
     * @var array<int, string>
     */
    private static $disallowed_STD3_mapped;

    /**
     * @var array<int, bool>
     */
    private static $disallowed_STD3_valid;

    /**
     * @var bool
     */
    private static $mappingTableLoaded = false;

    /**
     * @see https://www.unicode.org/reports/tr46/#ToASCII
     *
     * @param string $domainName
     * @param int    $options
     * @param int    $variant
     * @param array  $idna_info
     *
     * @return string|false
     */
    public static function idn_to_ascii($domainName, $options = self::IDNA_DEFAULT, $variant = self::INTL_IDNA_VARIANT_UTS46, &$idna_info = [])
    {
        if (\PHP_VERSION_ID > 80400 && '' === $domainName) {
            throw new \ValueError('idn_to_ascii(): Argument #1 ($domain) cannot be empty');
        }

        if (self::INTL_IDNA_VARIANT_2003 === $variant) {
            @trigger_error('idn_to_ascii(): INTL_IDNA_VARIANT_2003 is deprecated', \E_USER_DEPRECATED);
        }

        $options = [
            'CheckHyphens' => true,
            'CheckBidi' => self::INTL_IDNA_VARIANT_2003 === $variant || 0 !== ($options & self::IDNA_CHECK_BIDI),
            'CheckJoiners' => self::INTL_IDNA_VARIANT_UTS46 === $variant && 0 !== ($options & self::IDNA_CHECK_CONTEXTJ),
            'UseSTD3ASCIIRules' => 0 !== ($options & self::IDNA_USE_STD3_RULES),
            'Transitional_Processing' => self::INTL_IDNA_VARIANT_2003 === $variant || 0 === ($options & self::IDNA_NONTRANSITIONAL_TO_ASCII),
            'VerifyDnsLength' => true,
        ];
        $info = new Info();
        $labels = self::process((string) $domainName, $options, $info);

        foreach ($labels as $i => $label) {
            // Only convert labels to punycode that contain non-ASCII code points
            if (1 === preg_match('/[^\x00-\x7F]/', $label)) {
                try {
                    $label = 'xn--'.self::punycodeEncode($label);
                } catch (\Exception $e) {
                    $info->errors |= self::ERROR_PUNYCODE;
                }

                $labels[$i] = $label;
            }
        }

        if ($options['VerifyDnsLength']) {
            self::validateDomainAndLabelLength($labels, $info);
        }

        $idna_info = [
            'result' => implode('.', $labels),
            'isTransitionalDifferent' => $info->transitionalDifferent,
            'errors' => $info->errors,
        ];

        return 0 === $info->errors ? $idna_info['result'] : false;
    }

    /**
     * @see https://www.unicode.org/reports/tr46/#ToUnicode
     *
     * @param string $domainName
     * @param int    $options
     * @param int    $variant
     * @param array  $idna_info
     *
     * @return string|false
     */
    public static function idn_to_utf8($domainName, $options = self::IDNA_DEFAULT, $variant = self::INTL_IDNA_VARIANT_UTS46, &$idna_info = [])
    {
        if (\PHP_VERSION_ID > 80400 && '' === $domainName) {
            throw new \ValueError('idn_to_utf8(): Argument #1 ($domain) cannot be empty');
        }

        if (self::INTL_IDNA_VARIANT_2003 === $variant) {
            @trigger_error('idn_to_utf8(): INTL_IDNA_VARIANT_2003 is deprecated', \E_USER_DEPRECATED);
        }

        $info = new Info();
        $labels = self::process((string) $domainName, [
            'CheckHyphens' => true,
            'CheckBidi' => self::INTL_IDNA_VARIANT_2003 === $variant || 0 !== ($options & self::IDNA_CHECK_BIDI),
            'CheckJoiners' => self::INTL_IDNA_VARIANT_UTS46 === $variant && 0 !== ($options & self::IDNA_CHECK_CONTEXTJ),
            'UseSTD3ASCIIRules' => 0 !== ($options & self::IDNA_USE_STD3_RULES),
            'Transitional_Processing' => self::INTL_IDNA_VARIANT_2003 === $variant || 0 === ($options & self::IDNA_NONTRANSITIONAL_TO_UNICODE),
        ], $info);
        $idna_info = [
            'result' => implode('.', $labels),
            'isTransitionalDifferent' => $info->transitionalDifferent,
            'errors' => $info->errors,
        ];

        return 0 === $info->errors ? $idna_info['result'] : false;
    }

    /**
     * @param string $label
     *
     * @return bool
     */
    private static function isValidContextJ(array $codePoints, $label)
    {
        if (!isset(self::$virama)) {
            self::$virama = require __DIR__.\DIRECTORY_SEPARATOR.'Resources'.\DIRECTORY_SEPARATOR.'unidata'.\DIRECTORY_SEPARATOR.'virama.php';
        }

        $offset = 0;

        foreach ($codePoints as $i => $codePoint) {
            if (0x200C !== $codePoint && 0x200D !== $codePoint) {
                continue;
            }

            if (!isset($codePoints[$i - 1])) {
                return false;
            }

            // If Canonical_Combining_Class(Before(cp)) .eq. Virama Then True;
            if (isset(self::$virama[$codePoints[$i - 1]])) {
                continue;
            }

            // If RegExpMatch((Joining_Type:{L,D})(Joining_Type:T)*\u200C(Joining_Type:T)*(Joining_Type:{R,D})) Then
            // True;
            // Generated RegExp = ([Joining_Type:{L,D}][Joining_Type:T]*\u200C[Joining_Type:T]*)[Joining_Type:{R,D}]
            if (0x200C === $codePoint && 1 === preg_match(Regex::ZWNJ, $label, $matches, \PREG_OFFSET_CAPTURE, $offset)) {
                $offset += \strlen($matches[1][0]);

                continue;
            }

            return false;
        }

        return true;
    }

    /**
     * @see https://www.unicode.org/reports/tr46/#ProcessingStepMap
     *
     * @param string              $input
     * @param array<string, bool> $options
     *
     * @return string
     */
    private static function mapCodePoints($input, array $options, Info $info)
    {
        $str = '';
        $useSTD3ASCIIRules = $options['UseSTD3ASCIIRules'];
        $transitional = $options['Transitional_Processing'];

        foreach (self::utf8Decode($input) as $codePoint) {
            $data = self::lookupCodePointStatus($codePoint, $useSTD3ASCIIRules);

            switch ($data['status']) {
                case 'disallowed':
                case 'valid':
                    $str .= mb_chr($codePoint, 'utf-8');

                    break;

                case 'ignored':
                    // Do nothing.
                    break;

                case 'mapped':
                    $str .= $transitional && 0x1E9E === $codePoint ? 'ss' : $data['mapping'];

                    break;

                case 'deviation':
                    $info->transitionalDifferent = true;
                    $str .= ($transitional ? $data['mapping'] : mb_chr($codePoint, 'utf-8'));

                    break;
            }
        }

        return $str;
    }

    /**
     * @see https://www.unicode.org/reports/tr46/#Processing
     *
     * @param string              $domain
     * @param array<string, bool> $options
     *
     * @return array<int, string>
     */
    private static function process($domain, array $options, Info $info)
    {
        // If VerifyDnsLength is not set, we are doing ToUnicode otherwise we are doing ToASCII and
        // we need to respect the VerifyDnsLength option.
        $checkForEmptyLabels = !isset($options['VerifyDnsLength']) || $options['VerifyDnsLength'];

        if ($checkForEmptyLabels && '' === $domain) {
            $info->errors |= self::ERROR_EMPTY_LABEL;

            return [$domain];
        }

        // Step 1. Map each code point in the domain name string
        $domain = self::mapCodePoints($domain, $options, $info);

        // Step 2. Normalize the domain name string to Unicode Normalization Form C.
        if (!\Normalizer::isNormalized($domain, \Normalizer::FORM_C)) {
            $domain = \Normalizer::normalize($domain, \Normalizer::FORM_C);
        }

        // Step 3. Break the string into labels at U+002E (.) FULL STOP.
        $labels = explode('.', $domain);
        $lastLabelIndex = \count($labels) - 1;

        // Step 4. Convert and validate each label in the domain name string.
        foreach ($labels as $i => $label) {
            $validationOptions = $options;

            if ('xn--' === substr($label, 0, 4)) {
                // Step 4.1. If the label contains any non-ASCII code point (i.e., a code point greater than U+007F),
                // record that there was an error, and continue with the next label.
                if (preg_match('/[^\x00-\x7F]/', $label)) {
                    $info->errors |= self::ERROR_PUNYCODE;

                    continue;
                }

                // Step 4.2. Attempt to convert the rest of the label to Unicode according to Punycode [RFC3492]. If
                // that conversion fails, record that there was an error, and continue
                // with the next label. Otherwise replace the original label in the string by the results of the
                // conversion. Per UTS #46 revision 33, if the conversion succeeds but the result is empty or
                // contains only ASCII code points, record that there was an error and continue with the next label.
                try {
                    $label = self::punycodeDecode(substr($label, 4));
                } catch (\Exception $e) {
                    $info->errors |= self::ERROR_PUNYCODE;

                    continue;
                }

                if ('' === $label || 1 !== preg_match('/[^\x00-\x7F]/', $label)) {
                    $info->errors |= self::ERROR_INVALID_ACE_LABEL;

                    continue;
                }

                $validationOptions['Transitional_Processing'] = false;
                $labels[$i] = $label;
            }

            self::validateLabel($label, $info, $validationOptions, $i > 0 && $i === $lastLabelIndex);
        }

        if ($info->bidiDomain && !$info->validBidiDomain) {
            $info->errors |= self::ERROR_BIDI;
        }

        // Any input domain name string that does not record an error has been successfully
        // processed according to this specification. Conversely, if an input domain_name string
        // causes an error, then the processing of the input domain_name string fails. Determining
        // what to do with error input is up to the caller, and not in the scope of this document.
        return $labels;
    }

    /**
     * @see https://tools.ietf.org/html/rfc5893#section-2
     *
     * @param string $label
     */
    private static function validateBidiLabel($label, Info $info)
    {
        if (1 === preg_match(Regex::RTL_LABEL, $label)) {
            $info->bidiDomain = true;

            // Step 1. The first character must be a character with Bidi property L, R, or AL.
            // If it has the R or AL property, it is an RTL label
            if (1 !== preg_match(Regex::BIDI_STEP_1_RTL, $label)) {
                $info->validBidiDomain = false;

                return;
            }

            // Step 2. In an RTL label, only characters with the Bidi properties R, AL, AN, EN, ES,
            // CS, ET, ON, BN, or NSM are allowed.
            if (1 === preg_match(Regex::BIDI_STEP_2, $label)) {
                $info->validBidiDomain = false;

                return;
            }

            // Step 3. In an RTL label, the end of the label must be a character with Bidi property
            // R, AL, EN, or AN, followed by zero or more characters with Bidi property NSM.
            if (1 !== preg_match(Regex::BIDI_STEP_3, $label)) {
                $info->validBidiDomain = false;

                return;
            }

            // Step 4. In an RTL label, if an EN is present, no AN may be present, and vice versa.
            if (1 === preg_match(Regex::BIDI_STEP_4_AN, $label) && 1 === preg_match(Regex::BIDI_STEP_4_EN, $label)) {
                $info->validBidiDomain = false;

                return;
            }

            return;
        }

        // We are a LTR label
        // Step 1. The first character must be a character with Bidi property L, R, or AL.
        // If it has the L property, it is an LTR label.
        if (1 !== preg_match(Regex::BIDI_STEP_1_LTR, $label)) {
            $info->validBidiDomain = false;

            return;
        }

        // Step 5. In an LTR label, only characters with the Bidi properties L, EN,
        // ES, CS, ET, ON, BN, or NSM are allowed.
        if (1 === preg_match(Regex::BIDI_STEP_5, $label)) {
            $info->validBidiDomain = false;

            return;
        }

        // Step 6.In an LTR label, the end of the label must be a character with Bidi property L or
        // EN, followed by zero or more characters with Bidi property NSM.
        if (1 !== preg_match(Regex::BIDI_STEP_6, $label)) {
            $info->validBidiDomain = false;

            return;
        }
    }

    /**
     * @param array<int, string> $labels
     */
    private static function validateDomainAndLabelLength(array $labels, Info $info)
    {
        $maxDomainSize = self::MAX_DOMAIN_SIZE;
        $length = \count($labels);

        // Number of "." delimiters.
        $domainLength = $length - 1;

        // If the last label is empty and it is not the first label, then it is the root label.
        // Increase the max size by 1, making it 254, to account for the root label's "."
        // delimiter. This also means we don't need to check the last label's length for being too
        // long.
        if ($length > 1 && '' === $labels[$length - 1]) {
            ++$maxDomainSize;
            --$length;
        }

        for ($i = 0; $i < $length; ++$i) {
            $bytes = \strlen($labels[$i]);
            $domainLength += $bytes;

            if ($bytes > self::MAX_LABEL_SIZE) {
                $info->errors |= self::ERROR_LABEL_TOO_LONG;
            }
        }

        if ($domainLength > $maxDomainSize) {
            $info->errors |= self::ERROR_DOMAIN_NAME_TOO_LONG;
        }
    }

    /**
     * @see https://www.unicode.org/reports/tr46/#Validity_Criteria
     *
     * @param string              $label
     * @param array<string, bool> $options
     * @param bool                $canBeEmpty
     */
    private static function validateLabel($label, Info $info, array $options, $canBeEmpty)
    {
        if ('' === $label) {
            if (!$canBeEmpty && (!isset($options['VerifyDnsLength']) || $options['VerifyDnsLength'])) {
                $info->errors |= self::ERROR_EMPTY_LABEL;
            }

            return;
        }

        // Step 1. The label must be in Unicode Normalization Form C.
        if (!\Normalizer::isNormalized($label, \Normalizer::FORM_C)) {
            $info->errors |= self::ERROR_INVALID_ACE_LABEL;
        }

        $codePoints = self::utf8Decode($label);

        if ($options['CheckHyphens']) {
            // Step 2. If CheckHyphens, the label must not contain a U+002D HYPHEN-MINUS character
            // in both the thrid and fourth positions.
            if (isset($codePoints[2], $codePoints[3]) && 0x002D === $codePoints[2] && 0x002D === $codePoints[3]) {
                $info->errors |= self::ERROR_HYPHEN_3_4;
            }

            // Step 3. If CheckHyphens, the label must neither begin nor end with a U+002D
            // HYPHEN-MINUS character.
            if ('-' === substr($label, 0, 1)) {
                $info->errors |= self::ERROR_LEADING_HYPHEN;
            }

            if ('-' === substr($label, -1, 1)) {
                $info->errors |= self::ERROR_TRAILING_HYPHEN;
            }
        } elseif ('xn--' === substr($label, 0, 4)) {
            $info->errors |= self::ERROR_PUNYCODE;
        }

        // Step 4. The label must not contain a U+002E (.) FULL STOP.
        if (false !== strpos($label, '.')) {
            $info->errors |= self::ERROR_LABEL_HAS_DOT;
        }

        // Step 5. The label must not begin with a combining mark, that is: General_Category=Mark.
        if (1 === preg_match(Regex::COMBINING_MARK, $label)) {
            $info->errors |= self::ERROR_LEADING_COMBINING_MARK;
        }

        // Step 6. Each code point in the label must only have certain status values according to
        // Section 5, IDNA Mapping Table:
        $transitional = $options['Transitional_Processing'];
        $useSTD3ASCIIRules = $options['UseSTD3ASCIIRules'];

        foreach ($codePoints as $codePoint) {
            $data = self::lookupCodePointStatus($codePoint, $useSTD3ASCIIRules);
            $status = $data['status'];

            if ('valid' === $status || (!$transitional && 'deviation' === $status)) {
                continue;
            }

            $info->errors |= self::ERROR_DISALLOWED;

            break;
        }

        // Step 7. If CheckJoiners, the label must satisify the ContextJ rules from Appendix A, in
        // The Unicode Code Points and Internationalized Domain Names for Applications (IDNA)
        // [IDNA2008].
        if ($options['CheckJoiners'] && !self::isValidContextJ($codePoints, $label)) {
            $info->errors |= self::ERROR_CONTEXTJ;
        }

        // Step 8. If CheckBidi, and if the domain name is a  Bidi domain name, then the label must
        // satisfy all six of the numbered conditions in [IDNA2008] RFC 5893, Section 2.
        if ($options['CheckBidi'] && (!$info->bidiDomain || $info->validBidiDomain)) {
            self::validateBidiLabel($label, $info);
        }
    }

    /**
     * @see https://tools.ietf.org/html/rfc3492#section-6.2
     *
     * @param string $input
     *
     * @return string
     */
    private static function punycodeDecode($input)
    {
        $n = self::INITIAL_N;
        $out = 0;
        $i = 0;
        $bias = self::INITIAL_BIAS;
        $lastDelimIndex = strrpos($input, self::DELIMITER);
        $b = false === $lastDelimIndex ? 0 : $lastDelimIndex;
        $inputLength = \strlen($input);
        $output = [];
        $bytes = array_map('ord', str_split($input));

        for ($j = 0; $j < $b; ++$j) {
            if ($bytes[$j] > 0x7F) {
                throw new \Exception('Invalid input');
            }

            $output[$out++] = $input[$j];
        }

        if ($b > 0) {
            ++$b;
        }

        for ($in = $b; $in < $inputLength; ++$out) {
            $oldi = $i;
            $w = 1;

            for ($k = self::BASE; /* no condition */; $k += self::BASE) {
                if ($in >= $inputLength) {
                    throw new \Exception('Invalid input');
                }

                $digit = self::$basicToDigit[$bytes[$in++] & 0xFF];

                if ($digit < 0) {
                    throw new \Exception('Invalid input');
                }

                if ($digit > intdiv(self::MAX_INT - $i, $w)) {
                    throw new \Exception('Integer overflow');
                }

                $i += $digit * $w;

                if ($k <= $bias) {
                    $t = self::TMIN;
                } elseif ($k >= $bias + self::TMAX) {
                    $t = self::TMAX;
                } else {
                    $t = $k - $bias;
                }

                if ($digit < $t) {
                    break;
                }

                $baseMinusT = self::BASE - $t;

                if ($w > intdiv(self::MAX_INT, $baseMinusT)) {
                    throw new \Exception('Integer overflow');
                }

                $w *= $baseMinusT;
            }

            $outPlusOne = $out + 1;
            $bias = self::adaptBias($i - $oldi, $outPlusOne, 0 === $oldi);

            if (intdiv($i, $outPlusOne) > self::MAX_INT - $n) {
                throw new \Exception('Integer overflow');
            }

            $n += intdiv($i, $outPlusOne);
            $i %= $outPlusOne;
            array_splice($output, $i++, 0, [mb_chr($n, 'utf-8')]);
        }

        return implode('', $output);
    }

    /**
     * @see https://tools.ietf.org/html/rfc3492#section-6.3
     *
     * @param string $input
     *
     * @return string
     */
    private static function punycodeEncode($input)
    {
        $n = self::INITIAL_N;
        $delta = 0;
        $out = 0;
        $bias = self::INITIAL_BIAS;
        $inputLength = 0;
        $output = '';
        $iter = self::utf8Decode($input);

        foreach ($iter as $codePoint) {
            ++$inputLength;

            if ($codePoint < 0x80) {
                $output .= \chr($codePoint);
                ++$out;
            }
        }

        $h = $out;
        $b = $out;

        if ($b > 0) {
            $output .= self::DELIMITER;
            ++$out;
        }

        while ($h < $inputLength) {
            $m = self::MAX_INT;

            foreach ($iter as $codePoint) {
                if ($codePoint >= $n && $codePoint < $m) {
                    $m = $codePoint;
                }
            }

            if ($m - $n > intdiv(self::MAX_INT - $delta, $h + 1)) {
                throw new \Exception('Integer overflow');
            }

            $delta += ($m - $n) * ($h + 1);
            $n = $m;

            foreach ($iter as $codePoint) {
                if ($codePoint < $n && 0 === ++$delta) {
                    throw new \Exception('Integer overflow');
                }

                if ($codePoint === $n) {
                    $q = $delta;

                    for ($k = self::BASE; /* no condition */; $k += self::BASE) {
                        if ($k <= $bias) {
                            $t = self::TMIN;
                        } elseif ($k >= $bias + self::TMAX) {
                            $t = self::TMAX;
                        } else {
                            $t = $k - $bias;
                        }

                        if ($q < $t) {
                            break;
                        }

                        $qMinusT = $q - $t;
                        $baseMinusT = self::BASE - $t;
                        $output .= self::encodeDigit($t + $qMinusT % $baseMinusT, false);
                        ++$out;
                        $q = intdiv($qMinusT, $baseMinusT);
                    }

                    $output .= self::encodeDigit($q, false);
                    ++$out;
                    $bias = self::adaptBias($delta, $h + 1, $h === $b);
                    $delta = 0;
                    ++$h;
                }
            }

            ++$delta;
            ++$n;
        }

        return $output;
    }

    /**
     * @see https://tools.ietf.org/html/rfc3492#section-6.1
     *
     * @param int  $delta
     * @param int  $numPoints
     * @param bool $firstTime
     *
     * @return int
     */
    private static function adaptBias($delta, $numPoints, $firstTime)
    {
        // xxx >> 1 is a faster way of doing intdiv(xxx, 2)
        $delta = $firstTime ? intdiv($delta, self::DAMP) : $delta >> 1;
        $delta += intdiv($delta, $numPoints);
        $k = 0;

        while ($delta > ((self::BASE - self::TMIN) * self::TMAX) >> 1) {
            $delta = intdiv($delta, self::BASE - self::TMIN);
            $k += self::BASE;
        }

        return $k + intdiv((self::BASE - self::TMIN + 1) * $delta, $delta + self::SKEW);
    }

    /**
     * @param int  $d
     * @param bool $flag
     *
     * @return string
     */
    private static function encodeDigit($d, $flag)
    {
        return \chr($d + 22 + 75 * ($d < 26 ? 1 : 0) - (($flag ? 1 : 0) << 5));
    }

    /**
     * Takes a UTF-8 encoded string and converts it into a series of integer code points. Any
     * invalid byte sequences will be replaced by a U+FFFD replacement code point.
     *
     * @see https://encoding.spec.whatwg.org/#utf-8-decoder
     *
     * @param string $input
     *
     * @return array<int, int>
     */
    private static function utf8Decode($input)
    {
        $bytesSeen = 0;
        $bytesNeeded = 0;
        $lowerBoundary = 0x80;
        $upperBoundary = 0xBF;
        $codePoint = 0;
        $codePoints = [];
        $length = \strlen($input);

        for ($i = 0; $i < $length; ++$i) {
            $byte = \ord($input[$i]);

            if (0 === $bytesNeeded) {
                if ($byte >= 0x00 && $byte <= 0x7F) {
                    $codePoints[] = $byte;

                    continue;
                }

                if ($byte >= 0xC2 && $byte <= 0xDF) {
                    $bytesNeeded = 1;
                    $codePoint = $byte & 0x1F;
                } elseif ($byte >= 0xE0 && $byte <= 0xEF) {
                    if (0xE0 === $byte) {
                        $lowerBoundary = 0xA0;
                    } elseif (0xED === $byte) {
                        $upperBoundary = 0x9F;
                    }

                    $bytesNeeded = 2;
                    $codePoint = $byte & 0xF;
                } elseif ($byte >= 0xF0 && $byte <= 0xF4) {
                    if (0xF0 === $byte) {
                        $lowerBoundary = 0x90;
                    } elseif (0xF4 === $byte) {
                        $upperBoundary = 0x8F;
                    }

                    $bytesNeeded = 3;
                    $codePoint = $byte & 0x7;
                } else {
                    $codePoints[] = 0xFFFD;
                }

                continue;
            }

            if ($byte < $lowerBoundary || $byte > $upperBoundary) {
                $codePoint = 0;
                $bytesNeeded = 0;
                $bytesSeen = 0;
                $lowerBoundary = 0x80;
                $upperBoundary = 0xBF;
                --$i;
                $codePoints[] = 0xFFFD;

                continue;
            }

            $lowerBoundary = 0x80;
            $upperBoundary = 0xBF;
            $codePoint = ($codePoint << 6) | ($byte & 0x3F);

            if (++$bytesSeen !== $bytesNeeded) {
                continue;
            }

            $codePoints[] = $codePoint;
            $codePoint = 0;
            $bytesNeeded = 0;
            $bytesSeen = 0;
        }

        // String unexpectedly ended, so append a U+FFFD code point.
        if (0 !== $bytesNeeded) {
            $codePoints[] = 0xFFFD;
        }

        return $codePoints;
    }

    /**
     * @param int  $codePoint
     * @param bool $useSTD3ASCIIRules
     *
     * @return array{status: string, mapping?: string}
     */
    private static function lookupCodePointStatus($codePoint, $useSTD3ASCIIRules)
    {
        if (!self::$mappingTableLoaded) {
            self::$mappingTableLoaded = true;
            self::$mapped = require __DIR__.'/Resources/unidata/mapped.php';
            self::$ignored = require __DIR__.'/Resources/unidata/ignored.php';
            self::$deviation = require __DIR__.'/Resources/unidata/deviation.php';
            self::$disallowed = require __DIR__.'/Resources/unidata/disallowed.php';
            self::$disallowed_STD3_mapped = require __DIR__.'/Resources/unidata/disallowed_STD3_mapped.php';
            self::$disallowed_STD3_valid = require __DIR__.'/Resources/unidata/disallowed_STD3_valid.php';
        }

        if (isset(self::$mapped[$codePoint])) {
            return ['status' => 'mapped', 'mapping' => self::$mapped[$codePoint]];
        }

        if (isset(self::$ignored[$codePoint])) {
            return ['status' => 'ignored'];
        }

        if (isset(self::$deviation[$codePoint])) {
            return ['status' => 'deviation', 'mapping' => self::$deviation[$codePoint]];
        }

        if (isset(self::$disallowed[$codePoint]) || DisallowedRanges::inRange($codePoint)) {
            return ['status' => 'disallowed'];
        }

        $isDisallowedMapped = isset(self::$disallowed_STD3_mapped[$codePoint]);

        if ($isDisallowedMapped || isset(self::$disallowed_STD3_valid[$codePoint])) {
            $status = 'disallowed';

            if (!$useSTD3ASCIIRules) {
                $status = $isDisallowedMapped ? 'mapped' : 'valid';
            }

            if ($isDisallowedMapped) {
                return ['status' => $status, 'mapping' => self::$disallowed_STD3_mapped[$codePoint]];
            }

            return ['status' => $status];
        }

        return ['status' => 'valid'];
    }
}
