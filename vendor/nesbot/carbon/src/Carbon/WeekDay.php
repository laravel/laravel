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

enum WeekDay: int
{
    // Using constants is only safe starting from PHP 8.2
    case Sunday = 0; // CarbonInterface::SUNDAY
    case Monday = 1; // CarbonInterface::MONDAY
    case Tuesday = 2; // CarbonInterface::TUESDAY
    case Wednesday = 3; // CarbonInterface::WEDNESDAY
    case Thursday = 4; // CarbonInterface::THURSDAY
    case Friday = 5; // CarbonInterface::FRIDAY
    case Saturday = 6; // CarbonInterface::SATURDAY

    public static function int(self|int|null $value): ?int
    {
        return $value instanceof self ? $value->value : $value;
    }

    public static function fromNumber(int $number): self
    {
        $day = $number % CarbonInterface::DAYS_PER_WEEK;

        return self::from($day + ($day < 0 ? CarbonInterface::DAYS_PER_WEEK : 0));
    }

    public static function fromName(string $name, ?string $locale = null): self
    {
        try {
            return self::from(CarbonImmutable::parseFromLocale($name, $locale)->dayOfWeek);
        } catch (InvalidFormatException $exception) {
            // Possibly current language expect a dot after short name, but it's missing
            if ($locale !== null && !mb_strlen($name) < 4 && !str_ends_with($name, '.')) {
                try {
                    return self::from(CarbonImmutable::parseFromLocale($name.'.', $locale)->dayOfWeek);
                } catch (InvalidFormatException) {
                    // Throw previous error
                }
            }

            throw $exception;
        }
    }

    public function next(?CarbonImmutable $now = null): CarbonImmutable
    {
        return $now?->modify($this->name) ?? new CarbonImmutable($this->name);
    }

    public function locale(string $locale, ?CarbonImmutable $now = null): CarbonImmutable
    {
        return $this->next($now)->locale($locale);
    }
}
