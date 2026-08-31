<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\VarDumper\Caster;

use Symfony\Component\VarDumper\Cloner\Stub;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 * @author Jan Schädlich <jan.schaedlich@sensiolabs.de>
 *
 * @final
 *
 * @internal since Symfony 7.3
 */
class IntlCaster
{
    public static function castMessageFormatter(\MessageFormatter $c, array $a, Stub $stub, bool $isNested): array
    {
        $a += [
            Caster::PREFIX_VIRTUAL.'locale' => $c->getLocale(),
            Caster::PREFIX_VIRTUAL.'pattern' => $c->getPattern(),
        ];

        return self::castError($c, $a);
    }

    public static function castNumberFormatter(\NumberFormatter $c, array $a, Stub $stub, bool $isNested, int $filter = 0): array
    {
        $a += [
            Caster::PREFIX_VIRTUAL.'locale' => $c->getLocale(),
            Caster::PREFIX_VIRTUAL.'pattern' => $c->getPattern(),
        ];

        if ($filter & Caster::EXCLUDE_VERBOSE) {
            $stub->cut += 3;

            return self::castError($c, $a);
        }

        $a += [
            Caster::PREFIX_VIRTUAL.'attributes' => new EnumStub(
                [
                    'PARSE_INT_ONLY' => $c->getAttribute(\NumberFormatter::PARSE_INT_ONLY),
                    'GROUPING_USED' => $c->getAttribute(\NumberFormatter::GROUPING_USED),
                    'DECIMAL_ALWAYS_SHOWN' => $c->getAttribute(\NumberFormatter::DECIMAL_ALWAYS_SHOWN),
                    'MAX_INTEGER_DIGITS' => $c->getAttribute(\NumberFormatter::MAX_INTEGER_DIGITS),
                    'MIN_INTEGER_DIGITS' => $c->getAttribute(\NumberFormatter::MIN_INTEGER_DIGITS),
                    'INTEGER_DIGITS' => $c->getAttribute(\NumberFormatter::INTEGER_DIGITS),
                    'MAX_FRACTION_DIGITS' => $c->getAttribute(\NumberFormatter::MAX_FRACTION_DIGITS),
                    'MIN_FRACTION_DIGITS' => $c->getAttribute(\NumberFormatter::MIN_FRACTION_DIGITS),
                    'FRACTION_DIGITS' => $c->getAttribute(\NumberFormatter::FRACTION_DIGITS),
                    'MULTIPLIER' => $c->getAttribute(\NumberFormatter::MULTIPLIER),
                    'GROUPING_SIZE' => $c->getAttribute(\NumberFormatter::GROUPING_SIZE),
                    'ROUNDING_MODE' => $c->getAttribute(\NumberFormatter::ROUNDING_MODE),
                    'ROUNDING_INCREMENT' => $c->getAttribute(\NumberFormatter::ROUNDING_INCREMENT),
                    'FORMAT_WIDTH' => $c->getAttribute(\NumberFormatter::FORMAT_WIDTH),
                    'PADDING_POSITION' => $c->getAttribute(\NumberFormatter::PADDING_POSITION),
                    'SECONDARY_GROUPING_SIZE' => $c->getAttribute(\NumberFormatter::SECONDARY_GROUPING_SIZE),
                    'SIGNIFICANT_DIGITS_USED' => $c->getAttribute(\NumberFormatter::SIGNIFICANT_DIGITS_USED),
                    'MIN_SIGNIFICANT_DIGITS' => $c->getAttribute(\NumberFormatter::MIN_SIGNIFICANT_DIGITS),
                    'MAX_SIGNIFICANT_DIGITS' => $c->getAttribute(\NumberFormatter::MAX_SIGNIFICANT_DIGITS),
                    'LENIENT_PARSE' => $c->getAttribute(\NumberFormatter::LENIENT_PARSE),
                ]
            ),
            Caster::PREFIX_VIRTUAL.'text_attributes' => new EnumStub(
                [
                    'POSITIVE_PREFIX' => $c->getTextAttribute(\NumberFormatter::POSITIVE_PREFIX),
                    'POSITIVE_SUFFIX' => $c->getTextAttribute(\NumberFormatter::POSITIVE_SUFFIX),
                    'NEGATIVE_PREFIX' => $c->getTextAttribute(\NumberFormatter::NEGATIVE_PREFIX),
                    'NEGATIVE_SUFFIX' => $c->getTextAttribute(\NumberFormatter::NEGATIVE_SUFFIX),
                    'PADDING_CHARACTER' => $c->getTextAttribute(\NumberFormatter::PADDING_CHARACTER),
                    'CURRENCY_CODE' => $c->getTextAttribute(\NumberFormatter::CURRENCY_CODE),
                    'DEFAULT_RULESET' => $c->getTextAttribute(\NumberFormatter::DEFAULT_RULESET),
                    'PUBLIC_RULESETS' => $c->getTextAttribute(\NumberFormatter::PUBLIC_RULESETS),
                ]
            ),
            Caster::PREFIX_VIRTUAL.'symbols' => new EnumStub(
                [
                    'DECIMAL_SEPARATOR_SYMBOL' => $c->getSymbol(\NumberFormatter::DECIMAL_SEPARATOR_SYMBOL),
                    'GROUPING_SEPARATOR_SYMBOL' => $c->getSymbol(\NumberFormatter::GROUPING_SEPARATOR_SYMBOL),
                    'PATTERN_SEPARATOR_SYMBOL' => $c->getSymbol(\NumberFormatter::PATTERN_SEPARATOR_SYMBOL),
                    'PERCENT_SYMBOL' => $c->getSymbol(\NumberFormatter::PERCENT_SYMBOL),
                    'ZERO_DIGIT_SYMBOL' => $c->getSymbol(\NumberFormatter::ZERO_DIGIT_SYMBOL),
                    'DIGIT_SYMBOL' => $c->getSymbol(\NumberFormatter::DIGIT_SYMBOL),
                    'MINUS_SIGN_SYMBOL' => $c->getSymbol(\NumberFormatter::MINUS_SIGN_SYMBOL),
                    'PLUS_SIGN_SYMBOL' => $c->getSymbol(\NumberFormatter::PLUS_SIGN_SYMBOL),
                    'CURRENCY_SYMBOL' => $c->getSymbol(\NumberFormatter::CURRENCY_SYMBOL),
                    'INTL_CURRENCY_SYMBOL' => $c->getSymbol(\NumberFormatter::INTL_CURRENCY_SYMBOL),
                    'MONETARY_SEPARATOR_SYMBOL' => $c->getSymbol(\NumberFormatter::MONETARY_SEPARATOR_SYMBOL),
                    'EXPONENTIAL_SYMBOL' => $c->getSymbol(\NumberFormatter::EXPONENTIAL_SYMBOL),
                    'PERMILL_SYMBOL' => $c->getSymbol(\NumberFormatter::PERMILL_SYMBOL),
                    'PAD_ESCAPE_SYMBOL' => $c->getSymbol(\NumberFormatter::PAD_ESCAPE_SYMBOL),
                    'INFINITY_SYMBOL' => $c->getSymbol(\NumberFormatter::INFINITY_SYMBOL),
                    'NAN_SYMBOL' => $c->getSymbol(\NumberFormatter::NAN_SYMBOL),
                    'SIGNIFICANT_DIGIT_SYMBOL' => $c->getSymbol(\NumberFormatter::SIGNIFICANT_DIGIT_SYMBOL),
                    'MONETARY_GROUPING_SEPARATOR_SYMBOL' => $c->getSymbol(\NumberFormatter::MONETARY_GROUPING_SEPARATOR_SYMBOL),
                ]
            ),
        ];

        return self::castError($c, $a);
    }

    public static function castIntlTimeZone(\IntlTimeZone $c, array $a, Stub $stub, bool $isNested): array
    {
        $a += [
            Caster::PREFIX_VIRTUAL.'display_name' => $c->getDisplayName(),
            Caster::PREFIX_VIRTUAL.'id' => $c->getID(),
            Caster::PREFIX_VIRTUAL.'raw_offset' => $c->getRawOffset(),
        ];

        if ($c->useDaylightTime()) {
            $a += [
                Caster::PREFIX_VIRTUAL.'dst_savings' => $c->getDSTSavings(),
            ];
        }

        return self::castError($c, $a);
    }

    public static function castIntlCalendar(\IntlCalendar $c, array $a, Stub $stub, bool $isNested, int $filter = 0): array
    {
        $a += [
            Caster::PREFIX_VIRTUAL.'type' => $c->getType(),
            Caster::PREFIX_VIRTUAL.'first_day_of_week' => $c->getFirstDayOfWeek(),
            Caster::PREFIX_VIRTUAL.'minimal_days_in_first_week' => $c->getMinimalDaysInFirstWeek(),
            Caster::PREFIX_VIRTUAL.'repeated_wall_time_option' => $c->getRepeatedWallTimeOption(),
            Caster::PREFIX_VIRTUAL.'skipped_wall_time_option' => $c->getSkippedWallTimeOption(),
            Caster::PREFIX_VIRTUAL.'time' => $c->getTime(),
            Caster::PREFIX_VIRTUAL.'in_daylight_time' => $c->inDaylightTime(),
            Caster::PREFIX_VIRTUAL.'is_lenient' => $c->isLenient(),
            Caster::PREFIX_VIRTUAL.'time_zone' => ($filter & Caster::EXCLUDE_VERBOSE) ? new CutStub($c->getTimeZone()) : $c->getTimeZone(),
        ];

        return self::castError($c, $a);
    }

    public static function castIntlDateFormatter(\IntlDateFormatter $c, array $a, Stub $stub, bool $isNested, int $filter = 0): array
    {
        $a += [
            Caster::PREFIX_VIRTUAL.'locale' => $c->getLocale(),
            Caster::PREFIX_VIRTUAL.'pattern' => $c->getPattern(),
            Caster::PREFIX_VIRTUAL.'calendar' => $c->getCalendar(),
            Caster::PREFIX_VIRTUAL.'time_zone_id' => $c->getTimeZoneId(),
            Caster::PREFIX_VIRTUAL.'time_type' => $c->getTimeType(),
            Caster::PREFIX_VIRTUAL.'date_type' => $c->getDateType(),
            Caster::PREFIX_VIRTUAL.'calendar_object' => ($filter & Caster::EXCLUDE_VERBOSE) ? new CutStub($c->getCalendarObject()) : $c->getCalendarObject(),
            Caster::PREFIX_VIRTUAL.'time_zone' => ($filter & Caster::EXCLUDE_VERBOSE) ? new CutStub($c->getTimeZone()) : $c->getTimeZone(),
        ];

        return self::castError($c, $a);
    }

    private static function castError(object $c, array $a): array
    {
        if ($errorCode = $c->getErrorCode()) {
            $a += [
                Caster::PREFIX_VIRTUAL.'error_code' => $errorCode,
                Caster::PREFIX_VIRTUAL.'error_message' => $c->getErrorMessage(),
            ];
        }

        return $a;
    }
}
