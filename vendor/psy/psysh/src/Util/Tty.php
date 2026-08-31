<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Util;

/**
 * TTY detection utility.
 */
class Tty
{
    private static ?bool $sttySupported = null;
    private const DEFAULT_WIDTH = 100;

    /**
     * Check whether stty is available for terminal manipulation.
     *
     * Verifies that we're on a Unix-like system with shell_exec and a TTY
     * on stdin, and that stty actually works.
     */
    public static function supportsStty(): bool
    {
        if (self::$sttySupported !== null) {
            return (bool) self::$sttySupported;
        }

        if (\PHP_OS_FAMILY === 'Windows' || !\function_exists('shell_exec')) {
            return self::$sttySupported = false;
        }

        if (!\defined('STDIN') || !self::isatty(\STDIN)) {
            return self::$sttySupported = false;
        }

        $stty = @\shell_exec('stty -g 2>/dev/null');

        return self::$sttySupported = \is_string($stty) && \trim($stty) !== '';
    }

    /**
     * Check whether a stream is a TTY.
     *
     * Falls back gracefully when stream_isatty and posix_isatty are
     * unavailable, using fstat to check for a character device.
     *
     * Returns false when detection is uncertain.
     *
     * @param resource|int $stream
     */
    public static function isatty($stream): bool
    {
        if (\function_exists('stream_isatty')) {
            return @\stream_isatty($stream);
        }

        if (\function_exists('posix_isatty')) {
            return @\posix_isatty($stream);
        }

        // Fallback: check fstat mode for character device (TTY = 0020000)
        $stat = @\fstat($stream);
        if (!\is_array($stat) || !isset($stat['mode'])) {
            return false;
        }

        return ($stat['mode'] & 0170000) === 0020000;
    }

    /**
     * Get the current terminal width in columns.
     */
    public static function getWidth(int $default = self::DEFAULT_WIDTH): int
    {
        if (self::supportsStty() && \defined('STDOUT') && self::isatty(\STDOUT)) {
            // Output format: "rows cols"
            $size = @\shell_exec('stty size </dev/tty 2>/dev/null');
            if ($size && \preg_match('/^\d+ (\d+)$/', \trim($size), $matches)) {
                return (int) $matches[1];
            }

            $width = @\shell_exec('tput cols </dev/tty 2>/dev/null');
            if ($width && \is_numeric(\trim($width))) {
                return (int) \trim($width);
            }
        }

        // Check COLUMNS environment variable (may be stale after resize)
        $width = \getenv('COLUMNS');
        if ($width && \is_numeric(\trim($width))) {
            return (int) \trim($width);
        }

        return $default;
    }
}
