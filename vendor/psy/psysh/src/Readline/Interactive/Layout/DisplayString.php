<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Readline\Interactive\Layout;

use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Helper\Helper;

/**
 * Display-width string helpers for terminal rendering and navigation.
 *
 * Width modes:
 * - width: plain text width, no formatting/ANSI stripping.
 * - widthWithoutFormatting: parse formatter tags, then measure visible width.
 * - widthWithoutAnsi: strip ANSI/control sequences from rendered output.
 */
class DisplayString
{
    /**
     * ANSI/control-sequence regexes for rendered terminal output.
     */
    private const ANSI_CSI_RX = '/\x1B\[[0-9;?]*[ -\/]*[@-~]/';
    private const ANSI_OSC_RX = '/\x1B\][^\x07\x1B]*(?:\x07|\x1B\\\\)/';

    /**
     * Measure plain text width with no stripping.
     */
    /** @var bool|null */
    private static $hasHelperWidth;

    public static function width(string $text): int
    {
        // Helper::width() added in Symfony 5.3; fall back to strlen() for older versions.
        if (self::$hasHelperWidth ?? (self::$hasHelperWidth = \method_exists(Helper::class, 'width'))) {
            return Helper::width($text);
        }

        /* @phan-suppress-next-line PhanDeprecatedFunction BC fallback for Symfony < 5.3. */
        return Helper::strlen($text);
    }

    /**
     * Measure width after applying/removing formatter markup.
     *
     * Use this for strings that intentionally contain formatter tags like
     * "<info>...</info>" and should be measured by their rendered width.
     */
    public static function widthWithoutFormatting(string $text, OutputFormatterInterface $formatter): int
    {
        return self::width(Helper::removeDecoration($formatter, $text));
    }

    /**
     * Measure width from already-rendered terminal output.
     *
     * This strips ANSI/control sequences only; it does not parse formatter
     * markup like "<info>...</info>".
     */
    public static function widthWithoutAnsi(string $text): int
    {
        return self::width(self::stripAnsi($text));
    }

    /**
     * Resolve the code-point offset for a target display width.
     *
     * The returned offset is always on a grapheme boundary.
     */
    public static function offsetForWidth(string $text, int $targetWidth): int
    {
        if ($targetWidth <= 0 || $text === '') {
            return 0;
        }

        $offset = 0;
        $width = 0;
        foreach (self::graphemes($text) as $grapheme) {
            $graphemeWidth = self::width($grapheme);
            if ($width + $graphemeWidth > $targetWidth) {
                break;
            }

            $width += $graphemeWidth;
            $offset += \mb_strlen($grapheme);
        }

        return $offset;
    }

    /**
     * Truncate text to a maximum display width with optional ellipsis.
     */
    public static function truncate(string $text, int $maxWidth, bool $withEllipsis = false): string
    {
        if ($maxWidth <= 0 || $text === '') {
            return '';
        }

        if (self::width($text) <= $maxWidth) {
            return $text;
        }

        if (!$withEllipsis || $maxWidth <= 3) {
            return Helper::substr($text, 0, $maxWidth);
        }

        $targetWidth = $maxWidth - 3;
        $offset = self::offsetForWidth($text, $targetWidth);

        return \mb_substr($text, 0, $offset).'...';
    }

    /**
     * Iterate grapheme clusters in a string.
     *
     * @return string[]
     */
    private static function graphemes(string $text): array
    {
        if (\preg_match_all('/\X/u', $text, $matches) > 0) {
            return $matches[0];
        }

        // Fallback to individual code points when grapheme matching fails.
        $length = \mb_strlen($text);
        $chars = [];
        for ($i = 0; $i < $length; $i++) {
            $chars[] = \mb_substr($text, $i, 1);
        }

        return $chars;
    }

    /**
     * Remove terminal ANSI/control sequences from rendered text.
     */
    public static function stripAnsi(string $text): string
    {
        return \preg_replace([self::ANSI_CSI_RX, self::ANSI_OSC_RX], '', $text) ?? $text;
    }
}
