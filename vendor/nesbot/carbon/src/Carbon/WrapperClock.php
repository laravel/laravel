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

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Psr\Clock\ClockInterface as PsrClockInterface;
use RuntimeException;
use Symfony\Component\Clock\ClockInterface;

final class WrapperClock implements ClockInterface
{
    public function __construct(
        private PsrClockInterface|Factory|DateTimeInterface $currentClock,
    ) {
    }

    public function unwrap(): PsrClockInterface|Factory|DateTimeInterface
    {
        return $this->currentClock;
    }

    public function getFactory(): Factory
    {
        if ($this->currentClock instanceof Factory) {
            return $this->currentClock;
        }

        if ($this->currentClock instanceof DateTime) {
            $factory = new Factory();
            $factory->setTestNowAndTimezone($this->currentClock);

            return $factory;
        }

        if ($this->currentClock instanceof DateTimeImmutable) {
            $factory = new FactoryImmutable();
            $factory->setTestNowAndTimezone($this->currentClock);

            return $factory;
        }

        $factory = new FactoryImmutable();
        $factory->setTestNowAndTimezone(fn () => $this->currentClock->now());

        return $factory;
    }

    private function nowRaw(): DateTimeInterface
    {
        if ($this->currentClock instanceof DateTimeInterface) {
            return $this->currentClock;
        }

        if ($this->currentClock instanceof Factory) {
            return $this->currentClock->__call('now', []);
        }

        return $this->currentClock->now();
    }

    public function now(): DateTimeImmutable
    {
        $now = $this->nowRaw();

        return $now instanceof DateTimeImmutable
            ? $now
            : new CarbonImmutable($now);
    }

    /**
     * @template T of CarbonInterface
     *
     * @param class-string<T> $class
     *
     * @return T
     */
    public function nowAs(string $class, DateTimeZone|string|int|null $timezone = null): CarbonInterface
    {
        $now = $this->nowRaw();
        $date = $now instanceof $class ? $now : $class::instance($now);

        return $timezone === null ? $date : $date->setTimezone($timezone);
    }

    public function nowAsCarbon(DateTimeZone|string|int|null $timezone = null): CarbonInterface
    {
        $now = $this->nowRaw();

        return $now instanceof CarbonInterface
            ? ($timezone === null ? $now : $now->setTimezone($timezone))
            : $this->dateAsCarbon($now, $timezone);
    }

    private function dateAsCarbon(DateTimeInterface $date, DateTimeZone|string|int|null $timezone): CarbonInterface
    {
        return $date instanceof DateTimeImmutable
            ? new CarbonImmutable($date, $timezone)
            : new Carbon($date, $timezone);
    }

    public function sleep(float|int $seconds): void
    {
        if ($seconds === 0 || $seconds === 0.0) {
            return;
        }

        if ($seconds < 0) {
            throw new RuntimeException('Expected positive number of seconds, '.$seconds.' given');
        }

        if ($this->currentClock instanceof DateTimeInterface) {
            $this->currentClock = $this->addSeconds($this->currentClock, $seconds);

            return;
        }

        if ($this->currentClock instanceof ClockInterface) {
            $this->currentClock->sleep($seconds);

            return;
        }

        $this->currentClock = $this->addSeconds($this->currentClock->now(), $seconds);
    }

    public function withTimeZone(DateTimeZone|string $timezone): static
    {
        if ($this->currentClock instanceof ClockInterface) {
            return new self($this->currentClock->withTimeZone($timezone));
        }

        $now = $this->currentClock instanceof DateTimeInterface
            ? $this->currentClock
            : $this->currentClock->now();

        if (!($now instanceof DateTimeImmutable)) {
            $now = clone $now;
        }

        if (\is_string($timezone)) {
            $timezone = new DateTimeZone($timezone);
        }

        return new self($now->setTimezone($timezone));
    }

    private function addSeconds(DateTimeInterface $date, float|int $seconds): DateTimeInterface
    {
        $secondsPerHour = CarbonInterface::SECONDS_PER_MINUTE * CarbonInterface::MINUTES_PER_HOUR;
        $hours = number_format(
            floor($seconds / $secondsPerHour),
            thousands_separator: '',
        );
        $microseconds = number_format(
            ($seconds - $hours * $secondsPerHour) * CarbonInterface::MICROSECONDS_PER_SECOND,
            thousands_separator: '',
        );

        if (!($date instanceof DateTimeImmutable)) {
            $date = clone $date;
        }

        if ($hours !== '0') {
            $date = $date->modify("$hours hours");
        }

        if ($microseconds !== '0') {
            $date = $date->modify("$microseconds microseconds");
        }

        return $date;
    }
}
