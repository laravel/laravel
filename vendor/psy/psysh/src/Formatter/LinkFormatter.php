<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Formatter;

use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

/**
 * Utility for creating terminal hyperlinks (OSC 8).
 */
class LinkFormatter
{
    /** @var array<string, string> */
    private static $styles = [];

    /**
     * Set styles for formatting hyperlinks.
     *
     * @param array $styles Map of style name to inline style string
     */
    public static function setStyles(array $styles): void
    {
        self::$styles = $styles;
    }

    /**
     * Check if the current Symfony Console version supports hyperlinks.
     */
    public static function supportsLinks(): bool
    {
        static $supports = null;

        if ($supports === null) {
            $supports = \method_exists(OutputFormatterStyle::class, 'setHref');
        }

        return $supports;
    }

    /**
     * Wrap text in a style tag, optionally including an href.
     *
     * @param string      $style The style name (e.g., 'class', 'function', 'info')
     * @param string      $text  The text to wrap
     * @param string|null $href  Optional hyperlink
     *
     * @return string Formatted text with style and optional href
     */
    public static function styleWithHref(string $style, string $text, ?string $href = null): string
    {
        $escapedText = OutputFormatter::escape($text);

        if ($href !== null && self::supportsLinks()) {
            $href = self::encodeHrefForOsc8($href);
            $inline = self::$styles[$style] ?? '';
            $combinedStyle = $inline !== '' ? \sprintf('%s;href=%s', $inline, $href) : \sprintf('href=%s', $href);

            return \sprintf('<%s>%s</>', $combinedStyle, $escapedText);
        }

        return \sprintf('<%s>%s</%s>', $style, $escapedText, $style);
    }

    /**
     * Get the php.net manual URL for a given item.
     *
     * @param string $item Function or class name
     *
     * @return string URL to php.net manual
     */
    public static function getPhpNetUrl(string $item): string
    {
        // Normalize the item name for URL (lowercase, replace :: with . and _ with -)
        $normalized = \str_replace('::', '.', $item);
        $normalized = \str_replace('_', '-', $normalized);
        $normalized = \strtolower($normalized);

        return 'https://php.net/'.$normalized;
    }

    /**
     * Encode a string for use in OSC 8 hyperlink URIs.
     *
     * Per OSC 8 spec, URIs must only contain bytes in the 32-126 range.
     *
     * @param string $str String to encode
     *
     * @return string URI-encoded string safe for OSC 8
     */
    public static function encodeHrefForOsc8(string $str): string
    {
        // Encode any character outside printable ASCII range (32-126)
        return \preg_replace_callback('/[^\x20-\x7E]/', function ($matches) {
            return \rawurlencode($matches[0]);
        }, $str);
    }
}
