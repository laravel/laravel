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

use Psy\Readline\Interactive\Helper\DebugLog;

/**
 * Terminal color detection and computation utility.
 *
 * Queries the terminal's background and foreground colors via OSC escape
 * sequences, then computes an appropriate input frame background by blending
 * a subtle tint over the detected color.
 */
class TerminalColor
{
    /** @var array{bg: int[]|null, fg: int[]|null}|null */
    private static ?array $cachedColors = null;

    private const OSC_FOREGROUND = '10';
    private const OSC_BACKGROUND = '11';
    private const QUERY_TIMEOUT_USEC = 50000; // 50ms

    /**
     * Compute an appropriate input frame background color.
     *
     * Queries the terminal for its background color, then blends a subtle tint
     * on top: white at 12% for dark themes, black at 4% for light themes.
     *
     * Returns a hex color string (e.g. "#1a1a1a") or null if detection fails.
     */
    public static function computeInputFrameBackground(): ?string
    {
        return self::blendOverTerminalBackground([255, 255, 255], 0.12, [0, 0, 0], 0.04);
    }

    /**
     * Compute a red-tinted input frame background for syntax error feedback.
     *
     * Blends a subtle red tint over the terminal background: stronger for
     * dark themes (~15%), gentler for light themes (~8%).
     */
    public static function computeInputFrameErrorBackground(): ?string
    {
        return self::blendOverTerminalBackground([200, 60, 60], 0.15, [200, 60, 60], 0.08);
    }

    /**
     * Blend an overlay color over the detected terminal background.
     *
     * @param int[] $darkOverlay  Overlay [r, g, b] for dark themes
     * @param float $darkAlpha    Blend alpha for dark themes
     * @param int[] $lightOverlay Overlay [r, g, b] for light themes
     * @param float $lightAlpha   Blend alpha for light themes
     */
    private static function blendOverTerminalBackground(
        array $darkOverlay,
        float $darkAlpha,
        array $lightOverlay,
        float $lightAlpha
    ): ?string {
        $colors = self::queryTerminalColors();
        if ($colors['bg'] === null) {
            DebugLog::log('TerminalColor', 'NO_BG_DETECTED');

            return null;
        }

        $bg = $colors['bg'];

        if (self::isLight($bg)) {
            $blended = self::blend($lightOverlay, $bg, $lightAlpha);
        } else {
            $blended = self::blend($darkOverlay, $bg, $darkAlpha);
        }

        $hex = self::toHex($blended);

        DebugLog::log('TerminalColor', 'INPUT_FRAME', [
            'theme'   => self::isLight($bg) ? 'light' : 'dark',
            'bg'      => self::toHex($bg),
            'blended' => $hex,
        ]);

        return $hex;
    }

    /**
     * Query the terminal's foreground and background colors.
     *
     * Results are cached for the lifetime of the process.
     *
     * @return array{bg: int[]|null, fg: int[]|null}
     */
    public static function queryTerminalColors(): array
    {
        if (self::$cachedColors !== null) {
            return self::$cachedColors;
        }

        self::$cachedColors = ['bg' => null, 'fg' => null];

        $tty = self::openTty();
        if ($tty === null) {
            DebugLog::log('TerminalColor', 'SKIP', ['reason' => 'no_tty']);

            return self::$cachedColors;
        }

        $startTime = \microtime(true);

        try {
            $sttyState = self::saveStty();
            if ($sttyState === null) {
                DebugLog::log('TerminalColor', 'SKIP', ['reason' => 'stty_failed']);

                return self::$cachedColors;
            }

            try {
                // Put terminal in raw mode for direct I/O
                @\shell_exec('stty -echo -icanon min 0 time 0 < /dev/tty 2>/dev/null');

                // Query background and foreground together
                \fwrite($tty, "\033]".self::OSC_BACKGROUND.";?\033\\\033]".self::OSC_FOREGROUND.";?\033\\");
                \fflush($tty);

                $response = self::readResponse($tty);

                $elapsed = (\microtime(true) - $startTime) * 1000;

                if ($response !== '') {
                    self::$cachedColors['bg'] = self::parseOscResponse($response, self::OSC_BACKGROUND);
                    self::$cachedColors['fg'] = self::parseOscResponse($response, self::OSC_FOREGROUND);

                    DebugLog::log('TerminalColor', 'OSC_QUERY', [
                        'elapsed_ms' => \round($elapsed, 1),
                        'bg'         => self::$cachedColors['bg'] !== null ? self::toHex(self::$cachedColors['bg']) : 'null',
                        'fg'         => self::$cachedColors['fg'] !== null ? self::toHex(self::$cachedColors['fg']) : 'null',
                        'response'   => $response,
                    ]);
                } else {
                    DebugLog::log('TerminalColor', 'OSC_QUERY', [
                        'elapsed_ms' => \round($elapsed, 1),
                        'result'     => 'no_response',
                    ]);
                }
            } finally {
                self::restoreStty($sttyState);
            }
        } finally {
            @\fclose($tty);
        }

        return self::$cachedColors;
    }

    /**
     * Determine whether an RGB color is light (high luminance).
     *
     * @param int[] $rgb [r, g, b] values 0-255
     */
    public static function isLight(array $rgb): bool
    {
        return self::luminance($rgb) > 128;
    }

    /**
     * Compute perceived luminance for an RGB color.
     *
     * Uses the ITU-R BT.601 luma formula.
     *
     * @param int[] $rgb [r, g, b] values 0-255
     */
    public static function luminance(array $rgb): float
    {
        return 0.299 * $rgb[0] + 0.587 * $rgb[1] + 0.114 * $rgb[2];
    }

    /**
     * Alpha-composite an overlay color onto a base color.
     *
     * @param int[] $overlay [r, g, b] values 0-255
     * @param int[] $base    [r, g, b] values 0-255
     *
     * @return int[] [r, g, b] blended result
     */
    public static function blend(array $overlay, array $base, float $alpha): array
    {
        return [
            (int) \round($overlay[0] * $alpha + $base[0] * (1 - $alpha)),
            (int) \round($overlay[1] * $alpha + $base[1] * (1 - $alpha)),
            (int) \round($overlay[2] * $alpha + $base[2] * (1 - $alpha)),
        ];
    }

    /**
     * Convert an RGB array to a hex color string.
     *
     * @param int[] $rgb [r, g, b] values 0-255
     */
    public static function toHex(array $rgb): string
    {
        return \sprintf('#%02x%02x%02x', $rgb[0], $rgb[1], $rgb[2]);
    }

    /**
     * Parse an OSC color response for a given parameter number.
     *
     * Handles the `rgb:R/G/B`, `rgb:RR/GG/BB`, `rgb:RRR/GGG/BBB`, and
     * `rgb:RRRR/GGGG/BBBB` formats returned by xterm-compatible terminals.
     *
     * @return int[]|null [r, g, b] values 0-255, or null on parse failure
     */
    public static function parseOscResponse(string $response, string $param): ?array
    {
        // Match OSC response: ESC ] <param> ; rgb:R.../G.../B... (terminated by BEL or ST)
        $pattern = '/\033\]'.$param.';rgb:([0-9a-fA-F]{1,4})\/([0-9a-fA-F]{1,4})\/([0-9a-fA-F]{1,4})/';

        if (!\preg_match($pattern, $response, $matches)) {
            return null;
        }

        // Components are 1-4 hex digits; normalize each channel to 0-255.
        return [
            self::scaleColorComponent($matches[1]),
            self::scaleColorComponent($matches[2]),
            self::scaleColorComponent($matches[3]),
        ];
    }

    /**
     * Scale a hex color component string to an 8-bit value (0-255).
     *
     * Components are normalized by bit depth:
     * - 1 hex digit  (4-bit)  0x0-0xf      -> 0-255
     * - 2 hex digits (8-bit)  0x00-0xff    -> 0-255
     * - 3 hex digits (12-bit) 0x000-0xfff  -> 0-255
     * - 4 hex digits (16-bit) 0x0000-0xffff -> 0-255
     */
    private static function scaleColorComponent(string $hex): int
    {
        $digits = \strlen($hex);
        $value = \hexdec($hex);

        // 8-bit values are already in the target range.
        if ($digits === 2) {
            return (int) $value;
        }

        $maxValue = (16 ** $digits) - 1;

        return (int) \round(($value * 255) / $maxValue);
    }

    /**
     * Open /dev/tty for terminal I/O.
     *
     * @return resource|null
     */
    private static function openTty()
    {
        $tty = @\fopen('/dev/tty', 'r+');
        if ($tty === false) {
            return null;
        }

        \stream_set_blocking($tty, false);

        return $tty;
    }

    /**
     * Save the current stty state.
     */
    private static function saveStty(): ?string
    {
        $state = @\shell_exec('stty -g < /dev/tty 2>/dev/null');
        if (!\is_string($state) || \trim($state) === '') {
            return null;
        }

        return \trim($state);
    }

    /**
     * Restore a previously saved stty state.
     */
    private static function restoreStty(string $state): void
    {
        @\shell_exec(\sprintf('stty %s < /dev/tty 2>/dev/null', \escapeshellarg($state)));
    }

    /**
     * Read the terminal's response with a short timeout.
     *
     * @param resource $tty
     */
    private static function readResponse($tty): string
    {
        $response = '';
        $deadline = \microtime(true) + self::QUERY_TIMEOUT_USEC / 1000000;

        while (\microtime(true) < $deadline) {
            $read = [$tty];
            $write = null;
            $except = null;
            $remainingUsec = (int) (($deadline - \microtime(true)) * 1000000);
            if ($remainingUsec <= 0) {
                break;
            }

            $ready = @\stream_select($read, $write, $except, 0, $remainingUsec);
            if ($ready === false || $ready === 0) {
                // No data yet; if we already have some response, we're probably done
                if ($response !== '') {
                    break;
                }
                continue;
            }

            $chunk = @\fread($tty, 256);
            if ($chunk === false || $chunk === '') {
                break;
            }

            $response .= $chunk;

            // Stop if we've received both responses (two terminators, ST or BEL)
            $terminators = \substr_count($response, "\033\\") + \substr_count($response, "\x07");
            if ($terminators >= 2) {
                break;
            }
        }

        return $response;
    }

    /**
     * Reset the cached terminal colors.
     *
     * Primarily for testing.
     */
    public static function resetCache(): void
    {
        self::$cachedColors = null;
    }
}
