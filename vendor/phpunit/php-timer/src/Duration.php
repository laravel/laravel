<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-timer.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\Timer;

use function floor;
use function sprintf;

/**
 * @immutable
 */
final readonly class Duration
{
    private float $nanoseconds;
    private int $hours;
    private int $minutes;
    private int $seconds;
    private int $milliseconds;

    public static function fromMicroseconds(float $microseconds): self
    {
        return new self($microseconds * 1000);
    }

    public static function fromNanoseconds(float $nanoseconds): self
    {
        return new self($nanoseconds);
    }

    private function __construct(float $nanoseconds)
    {
        $this->nanoseconds     = $nanoseconds;
        $timeInMilliseconds    = $nanoseconds / 1000000;
        $hours                 = floor($timeInMilliseconds / 60 / 60 / 1000);
        $hoursInMilliseconds   = $hours * 60 * 60 * 1000;
        $minutes               = floor($timeInMilliseconds / 60 / 1000) % 60;
        $minutesInMilliseconds = $minutes * 60 * 1000;
        $seconds               = floor(($timeInMilliseconds - $hoursInMilliseconds - $minutesInMilliseconds) / 1000);
        $secondsInMilliseconds = $seconds * 1000;
        $milliseconds          = $timeInMilliseconds - $hoursInMilliseconds - $minutesInMilliseconds - $secondsInMilliseconds;
        $this->hours           = (int) $hours;
        $this->minutes         = $minutes;
        $this->seconds         = (int) $seconds;
        $this->milliseconds    = (int) $milliseconds;
    }

    public function asNanoseconds(): float
    {
        return $this->nanoseconds;
    }

    public function asMicroseconds(): float
    {
        return $this->nanoseconds / 1000;
    }

    public function asMilliseconds(): float
    {
        return $this->nanoseconds / 1000000;
    }

    public function asSeconds(): float
    {
        return $this->nanoseconds / 1000000000;
    }

    public function asString(): string
    {
        $result = '';

        if ($this->hours > 0) {
            $result = sprintf('%02d', $this->hours) . ':';
        }

        $result .= sprintf('%02d', $this->minutes) . ':';
        $result .= sprintf('%02d', $this->seconds);

        if ($this->milliseconds > 0) {
            $result .= '.' . sprintf('%03d', $this->milliseconds);
        }

        return $result;
    }
}
