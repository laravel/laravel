<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Clock;

/**
 * A monotonic clock suitable for performance profiling.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
final class MonotonicClock implements ClockInterface
{
    private int $sOffset;
    private int $usOffset;
    private \DateTimeZone $timezone;

    /**
     * @throws \DateInvalidTimeZoneException When $timezone is invalid
     */
    public function __construct(\DateTimeZone|string|null $timezone = null)
    {
        if (false === $offset = hrtime()) {
            throw new \RuntimeException('hrtime() returned false: the runtime environment does not provide access to a monotonic timer.');
        }

        $time = explode(' ', microtime(), 2);
        $this->sOffset = $time[1] - $offset[0];
        $this->usOffset = (int) ($time[0] * 1000000) - (int) ($offset[1] / 1000);

        $this->timezone = \is_string($timezone ??= date_default_timezone_get()) ? $this->withTimeZone($timezone)->timezone : $timezone;
    }

    public function now(): DatePoint
    {
        [$s, $us] = hrtime();

        if (1000000 <= $us = (int) ($us / 1000) + $this->usOffset) {
            ++$s;
            $us -= 1000000;
        } elseif (0 > $us) {
            --$s;
            $us += 1000000;
        }

        if (6 !== \strlen($now = (string) $us)) {
            $now = str_pad($now, 6, '0', \STR_PAD_LEFT);
        }

        $now = '@'.($s + $this->sOffset).'.'.$now;

        return DatePoint::createFromInterface(new \DateTimeImmutable($now, $this->timezone))->setTimezone($this->timezone);
    }

    public function sleep(float|int $seconds): void
    {
        if (0 < $s = (int) $seconds) {
            sleep($s);
        }

        if (0 < $us = $seconds - $s) {
            usleep((int) ($us * 1E6));
        }
    }

    /**
     * @throws \DateInvalidTimeZoneException When $timezone is invalid
     */
    public function withTimeZone(\DateTimeZone|string $timezone): static
    {
        if (\PHP_VERSION_ID >= 80300 && \is_string($timezone)) {
            $timezone = new \DateTimeZone($timezone);
        } elseif (\is_string($timezone)) {
            try {
                $timezone = new \DateTimeZone($timezone);
            } catch (\Exception $e) {
                throw new \DateInvalidTimeZoneException($e->getMessage(), $e->getCode(), $e);
            }
        }

        $clone = clone $this;
        $clone->timezone = $timezone;

        return $clone;
    }
}
