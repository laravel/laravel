<?php

/**
 * League.Uri (https://uri.thephpleague.com)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\Uri\Idna;

use BackedEnum;
use League\Uri\Exceptions\ConversionFailed;
use League\Uri\Exceptions\SyntaxError;
use League\Uri\FeatureDetection;
use Stringable;

use function idn_to_ascii;
use function idn_to_utf8;
use function rawurldecode;
use function strtolower;

use const INTL_IDNA_VARIANT_UTS46;

/**
 * @see https://unicode-org.github.io/icu-docs/apidoc/released/icu4c/uidna_8h.html
 */
final class Converter
{
    private const REGEXP_IDNA_PATTERN = '/[^\x20-\x7f]/';
    private const MAX_DOMAIN_LENGTH = 253;
    private const MAX_LABEL_LENGTH = 63;

    /**
     * General registered name regular expression.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.2.2
     * @see https://regex101.com/r/fptU8V/1
     */
    private const REGEXP_REGISTERED_NAME = '/
        (?(DEFINE)
            (?<unreserved>[a-z0-9_~\-])   # . is missing as it is used to separate labels
            (?<sub_delims>[!$&\'()*+,;=])
            (?<encoded>%[A-F0-9]{2})
            (?<reg_name>(?:(?&unreserved)|(?&sub_delims)|(?&encoded))*)
        )
            ^(?:(?&reg_name)\.)*(?&reg_name)\.?$
        /ix';

    /**
     * Converts the input to its IDNA ASCII form or throw on failure.
     *
     * @see Converter::toAscii()
     *
     * @throws SyntaxError if the string cannot be converted to UNICODE using IDN UTS46 algorithm
     * @throws ConversionFailed if the conversion returns error
     */
    public static function toAsciiOrFail(BackedEnum|Stringable|string $domain, Option|int|null $options = null): string
    {
        $result = self::toAscii($domain, $options);

        return match (true) {
            $result->hasErrors() => throw ConversionFailed::dueToIdnError($domain, $result),
            default => $result->domain(),
        };
    }

    /**
     * Converts the input to its IDNA ASCII form.
     *
     * This method returns the string converted to IDN ASCII form
     *
     * @throws SyntaxError if the string cannot be converted to ASCII using IDN UTS46 algorithm
     */
    public static function toAscii(BackedEnum|Stringable|string $domain, Option|int|null $options = null): Result
    {
        if ($domain instanceof BackedEnum) {
            $domain = $domain->value;
        }

        $domain = rawurldecode((string) $domain);

        if (1 === preg_match(self::REGEXP_IDNA_PATTERN, $domain)) {
            FeatureDetection::supportsIdn();

            $flags = match (true) {
                null === $options => Option::forIDNA2008Ascii(),
                $options instanceof Option => $options,
                default => Option::new($options),
            };

            idn_to_ascii($domain, $flags->toBytes(), INTL_IDNA_VARIANT_UTS46, $idnaInfo);

            if ([] === $idnaInfo) {
                return Result::fromIntl([
                    'result' => strtolower($domain),
                    'isTransitionalDifferent' => false,
                    'errors' => self::validateDomainAndLabelLength($domain),
                ]);
            }

            return Result::fromIntl($idnaInfo);
        }

        $error = Error::NONE->value;
        if (1 !== preg_match(self::REGEXP_REGISTERED_NAME, $domain)) {
            $error |= Error::DISALLOWED->value;
        }

        return Result::fromIntl([
            'result' => strtolower($domain),
            'isTransitionalDifferent' => false,
            'errors' => self::validateDomainAndLabelLength($domain) | $error,
        ]);
    }

    /**
     * Converts the input to its IDNA UNICODE form or throw on failure.
     *
     * @see Converter::toUnicode()
     *
     * @throws ConversionFailed if the conversion returns error
     */
    public static function toUnicodeOrFail(BackedEnum|Stringable|string $domain, Option|int|null $options = null): string
    {
        $result = self::toUnicode($domain, $options);

        return match (true) {
            $result->hasErrors() => throw ConversionFailed::dueToIdnError($domain, $result),
            default => $result->domain(),
        };
    }

    /**
     * Converts the input to its IDNA UNICODE form.
     *
     * This method returns the string converted to IDN UNICODE form
     *
     * @throws SyntaxError if the string cannot be converted to UNICODE using IDN UTS46 algorithm
     */
    public static function toUnicode(BackedEnum|Stringable|string $domain, Option|int|null $options = null): Result
    {
        if ($domain instanceof BackedEnum) {
            $domain = $domain->value;
        }

        $domain = rawurldecode((string) $domain);
        if (false === stripos($domain, 'xn--')) {
            return Result::fromIntl(['result' => strtolower($domain), 'isTransitionalDifferent' => false, 'errors' => Error::NONE->value]);
        }

        FeatureDetection::supportsIdn();

        $flags = match (true) {
            null === $options => Option::forIDNA2008Unicode(),
            $options instanceof Option => $options,
            default => Option::new($options),
        };

        idn_to_utf8($domain, $flags->toBytes(), INTL_IDNA_VARIANT_UTS46, $idnaInfo);

        if ([] === $idnaInfo) {
            return Result::fromIntl(['result' => strtolower($domain), 'isTransitionalDifferent' => false, 'errors' => Error::NONE->value]);
        }

        return Result::fromIntl($idnaInfo);
    }

    /**
     * Tells whether the submitted host is a valid IDN regardless of its format.
     *
     * Returns false if the host is invalid or if its conversion yields the same result
     */
    public static function isIdn(BackedEnum|Stringable|string|null $domain): bool
    {
        if ($domain instanceof BackedEnum) {
            $domain = $domain->value;
        }

        $domain = strtolower(rawurldecode((string) $domain));
        $result = match (1) {
            preg_match(self::REGEXP_IDNA_PATTERN, $domain) => self::toAscii($domain),
            default => self::toUnicode($domain),
        };

        return match (true) {
            $result->hasErrors() => false,
            default => $result->domain() !== $domain,
        };
    }

    /**
     * Adapted from https://github.com/TRowbotham/idna.
     *
     * @see https://github.com/TRowbotham/idna/blob/master/src/Idna.php#L236
     */
    private static function validateDomainAndLabelLength(string $domain): int
    {
        $error = Error::NONE->value;
        $labels = explode('.', $domain);
        $maxDomainSize = self::MAX_DOMAIN_LENGTH;
        $length = count($labels);

        // If the last label is empty, and it is not the first label, then it is the root label.
        // Increase the max size by 1, making it 254, to account for the root label's "."
        // delimiter. This also means we don't need to check the last label's length for being too
        // long.
        if ($length > 1 && '' === $labels[$length - 1]) {
            ++$maxDomainSize;
            array_pop($labels);
        }

        if (strlen($domain) > $maxDomainSize) {
            $error |= Error::DOMAIN_NAME_TOO_LONG->value;
        }

        foreach ($labels as $label) {
            if (strlen($label) > self::MAX_LABEL_LENGTH) {
                $error |= Error::LABEL_TOO_LONG->value;

                break;
            }
        }

        return $error;
    }
}
