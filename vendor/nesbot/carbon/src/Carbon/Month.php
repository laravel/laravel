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

namespace Carbon;

use Carbon\Exceptions\InvalidFormatException;

enum Month: int
{
    // Using constants is only safe starting from PHP 8.2
    case January = 1; // CarbonInterface::JANUARY
    case February = 2; // CarbonInterface::FEBRUARY
    case March = 3; // CarbonInterface::MARCH
    case April = 4; // CarbonInterface::APRIL
    case May = 5; // CarbonInterface::MAY
    case June = 6; // CarbonInterface::JUNE
    case July = 7; // CarbonInterface::JULY
    case August = 8; // CarbonInterface::AUGUST
    case September = 9; // CarbonInterface::SEPTEMBER
    case October = 10; // CarbonInterface::OCTOBER
    case November = 11; // CarbonInterface::NOVEMBER
    case December = 12; // CarbonInterface::DECEMBER

    public static function int(self|int|null $value): ?int
    {
        return $value instanceof self ? $value->value : $value;
    }

    public static function fromNumber(int $number): self
    {
        $month = $number % CarbonInterface::MONTHS_PER_YEAR;

        return self::from($month + ($month < 1 ? CarbonInterface::MONTHS_PER_YEAR : 0));
    }

    public static function fromName(string $name, ?string $locale = null): self
    {
        try {
            return self::from(CarbonImmutable::parseFromLocale("$name 1", $locale)->month);
        } catch (InvalidFormatException $exception) {
            // Possibly current language expect a dot after short name, but it's missing
            if ($locale !== null && !mb_strlen($name) < 4 && !str_ends_with($name, '.')) {
                try {
                    return self::from(CarbonImmutable::parseFromLocale("$name. 1", $locale)->month);
                } catch (InvalidFormatException $e) {
                    // Throw previous error
                }
            }

            throw $exception;
        }
    }

    public function ofTheYear(CarbonImmutable|int|null $now = null): CarbonImmutable
    {
        if (\is_int($now)) {
            return CarbonImmutable::create($now, $this->value);
        }

        $modifier = $this->name.' 1st';

        return $now?->modify($modifier) ?? new CarbonImmutable($modifier);
    }

    public function locale(string $locale, ?CarbonImmutable $now = null): CarbonImmutable
    {
        return $this->ofTheYear($now)->locale($locale);
    }
}
