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

enum Unit: string
{
    case Microsecond = 'microsecond';
    case Millisecond = 'millisecond';
    case Second = 'second';
    case Minute = 'minute';
    case Hour = 'hour';
    case Day = 'day';
    case Week = 'week';
    case Month = 'month';
    case Quarter = 'quarter';
    case Year = 'year';
    case Decade = 'decade';
    case Century = 'century';
    case Millennium = 'millennium';

    public static function toName(self|string $unit): string
    {
        return $unit instanceof self ? $unit->value : $unit;
    }

    /** @internal */
    public static function toNameIfUnit(mixed $unit): mixed
    {
        return $unit instanceof self ? $unit->value : $unit;
    }

    public static function fromName(string $name, ?string $locale = null): self
    {
        if ($locale !== null) {
            $messages = Translator::get($locale)->getMessages($locale) ?? [];

            if ($messages !== []) {
                $lowerName = mb_strtolower($name);

                foreach (self::cases() as $unit) {
                    foreach (['', '_from_now', '_ago', '_after', '_before'] as $suffix) {
                        $message = $messages[$unit->value.$suffix] ?? null;

                        if (\is_string($message)) {
                            $words = explode('|', mb_strtolower(preg_replace(
                                '/[{\[\]].+?[}\[\]]/',
                                '',
                                str_replace(':count', '', $message),
                            )));

                            foreach ($words as $word) {
                                if (trim($word) === $lowerName) {
                                    return $unit;
                                }
                            }
                        }
                    }
                }
            }
        }

        return self::from(CarbonImmutable::singularUnit($name));
    }

    public function singular(?string $locale = null): string
    {
        if ($locale !== null) {
            return trim(Translator::get($locale)->trans($this->value, [
                '%count%' => 1,
                ':count' => 1,
            ]), "1 \n\r\t\v\0");
        }

        return $this->value;
    }

    public function plural(?string $locale = null): string
    {
        if ($locale !== null) {
            return trim(Translator::get($locale)->trans($this->value, [
                '%count%' => 9,
                ':count' => 9,
            ]), "9 \n\r\t\v\0");
        }

        return CarbonImmutable::pluralUnit($this->value);
    }

    public function interval(int|float $value = 1): CarbonInterval
    {
        return CarbonInterval::fromString("$value $this->name");
    }

    public function locale(string $locale): CarbonInterval
    {
        return $this->interval()->locale($locale);
    }

    public function toPeriod(...$params): CarbonPeriod
    {
        return $this->interval()->toPeriod(...$params);
    }

    public function stepBy(mixed $interval, Unit|string|null $unit = null): CarbonPeriod
    {
        return $this->interval()->stepBy($interval, $unit);
    }
}
