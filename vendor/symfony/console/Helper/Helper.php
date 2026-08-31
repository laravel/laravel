<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Helper;

use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\String\UnicodeString;

/**
 * Helper is the base class for all helper classes.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class Helper implements HelperInterface
{
    protected ?HelperSet $helperSet = null;

    public function setHelperSet(?HelperSet $helperSet): void
    {
        $this->helperSet = $helperSet;
    }

    public function getHelperSet(): ?HelperSet
    {
        return $this->helperSet;
    }

    /**
     * Returns the width of a string, using mb_strwidth if it is available.
     * The width is how many characters positions the string will use.
     */
    public static function width(?string $string): int
    {
        $string ??= '';

        if (preg_match('//u', $string)) {
            $string = preg_replace('/[\p{Cc}\x7F]++/u', '', $string, -1, $count);

            return (new UnicodeString($string))->width(false) + $count;
        }

        if (false === $encoding = mb_detect_encoding($string, null, true)) {
            return \strlen($string);
        }

        return mb_strwidth($string, $encoding);
    }

    /**
     * Returns the length of a string, using mb_strlen if it is available.
     * The length is related to how many bytes the string will use.
     */
    public static function length(?string $string): int
    {
        $string ??= '';

        if (preg_match('//u', $string)) {
            return (new UnicodeString($string))->length();
        }

        if (false === $encoding = mb_detect_encoding($string, null, true)) {
            return \strlen($string);
        }

        return mb_strlen($string, $encoding);
    }

    /**
     * Returns the subset of a string, using mb_substr if it is available.
     */
    public static function substr(?string $string, int $from, ?int $length = null): string
    {
        $string ??= '';

        if (preg_match('//u', $string)) {
            return (new UnicodeString($string))->slice($from, $length);
        }

        if (false === $encoding = mb_detect_encoding($string, null, true)) {
            return substr($string, $from, $length);
        }

        return mb_substr($string, $from, $length, $encoding);
    }

    public static function formatTime(int|float $secs, int $precision = 1): string
    {
        $ms = (int) ($secs * 1000);
        $secs = (int) floor($secs);

        if (0 === $ms) {
            return '< 1 ms';
        }

        static $timeFormats = [
            [1, 'ms'],
            [1000, 's'],
            [60000, 'min'],
            [3600000, 'h'],
            [86_400_000, 'd'],
        ];

        $times = [];
        foreach ($timeFormats as $index => $format) {
            $milliSeconds = isset($timeFormats[$index + 1]) ? $ms % $timeFormats[$index + 1][0] : $ms;

            if (isset($times[$index - $precision])) {
                unset($times[$index - $precision]);
            }

            if (0 === $milliSeconds) {
                continue;
            }

            $unitCount = ($milliSeconds / $format[0]);
            $times[$index] = $unitCount.' '.$format[1];

            if ($ms === $milliSeconds) {
                break;
            }

            $ms -= $milliSeconds;
        }

        return implode(', ', array_reverse($times));
    }

    public static function formatMemory(int $memory): string
    {
        if ($memory >= 1024 * 1024 * 1024) {
            return \sprintf('%.1f GiB', $memory / 1024 / 1024 / 1024);
        }

        if ($memory >= 1024 * 1024) {
            return \sprintf('%.1f MiB', $memory / 1024 / 1024);
        }

        if ($memory >= 1024) {
            return \sprintf('%d KiB', $memory / 1024);
        }

        return \sprintf('%d B', $memory);
    }

    public static function removeDecoration(OutputFormatterInterface $formatter, ?string $string): string
    {
        $isDecorated = $formatter->isDecorated();
        $formatter->setDecorated(false);
        // remove <...> formatting
        $string = $formatter->format($string ?? '');
        // remove already formatted characters
        $string = preg_replace("/\033\[[^m]*m/", '', $string ?? '');
        // remove terminal hyperlinks
        $string = preg_replace('/\\033]8;[^;]*;[^\\033]*\\033\\\\/', '', $string ?? '');
        $formatter->setDecorated($isDecorated);

        return $string;
    }
}
