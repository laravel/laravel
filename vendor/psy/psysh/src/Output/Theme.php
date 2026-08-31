<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Output;

use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

/**
 * An output Theme, which controls prompt strings, formatter styles, and compact output.
 */
class Theme
{
    public const BUILTIN_THEMES = ['modern', 'compact', 'classic'];

    const MODERN_THEME = []; // Defaults :)

    const COMPACT_THEME = [
        'compact' => true,
    ];

    const CLASSIC_THEME = [
        'compact' => true,

        'prompt'       => '>>> ',
        'bufferPrompt' => '... ',
        'replayPrompt' => '--> ',
        'returnValue'  => '=>  ',
    ];

    private const BUILTIN_THEME_CONFIGS = [
        'modern'  => self::MODERN_THEME,
        'compact' => self::COMPACT_THEME,
        'classic' => self::CLASSIC_THEME,
    ];

    // Custom themes fall back to DEFAULT_STYLES for any undefined style.
    const DEFAULT_STYLES = [
        'info'    => ['green', null, ['bold']],
        'warning' => ['black', 'yellow'],
        'error'   => ['white', 'red', ['bold']],
        'whisper' => ['gray'],

        'aside'            => ['blue'],
        'strong'           => [null, null, ['bold']],
        'return'           => ['cyan'],
        'urgent'           => ['red'],
        'hidden'           => ['black'],
        'command'          => ['cyan', null, ['bold']],
        'command_option'   => ['yellow'],
        'command_argument' => ['green'],

        // Keywords
        'public'    => [null, null, ['bold']],
        'protected' => ['yellow'],
        'private'   => ['red'],
        'global'    => ['cyan', null, ['bold']],
        'const'     => ['cyan'],
        'class'     => ['blue', null, ['underscore']],
        'function'  => [null],
        'virtual'   => ['magenta'],
        'default'   => [null],

        // Types
        'number'       => ['magenta'],
        'integer'      => ['magenta'],
        'float'        => ['magenta'],
        'string'       => ['green'],
        'array_key'    => ['blue'],
        'bool'         => ['cyan'],
        'keyword'      => ['yellow'],
        'comment'      => ['blue'],
        'code_comment' => ['gray'],
        'object'       => ['blue'],
        'resource'     => ['yellow'],

        // Code-specific formatting
        'inline_html' => ['cyan'],

        // Interactive readline
        'input_frame'       => ['bright-white', 'gray'],
        'input_frame_error' => ['bright-white', 'red'],
        'input_highlight'   => [null, null, ['reverse']],
    ];

    const ERROR_STYLES = ['info', 'warning', 'error', 'whisper', 'class'];

    private bool $compact = false;

    private string $prompt = '> ';
    private string $bufferPrompt = '. ';
    private string $replayPrompt = '- ';
    private string $returnValue = '= ';

    private string $grayFallback = 'blue';

    private array $styles = [];

    /**
     * @param string|array $config theme name or config options
     */
    public function __construct($config = 'modern')
    {
        if (\is_string($config)) {
            if (isset(self::BUILTIN_THEME_CONFIGS[$config])) {
                $config = self::BUILTIN_THEME_CONFIGS[$config];
            } else {
                \trigger_error(\sprintf('Unknown theme: %s', $config), \E_USER_NOTICE);
                $config = static::MODERN_THEME;
            }
        }

        if (!\is_array($config)) {
            throw new \InvalidArgumentException('Invalid theme config');
        }

        foreach ($config as $name => $value) {
            switch ($name) {
                case 'compact':
                    $this->setCompact($value);
                    break;

                case 'prompt':
                    $this->setPrompt($value);
                    break;

                case 'bufferPrompt':
                    $this->setBufferPrompt($value);
                    break;

                case 'replayPrompt':
                    $this->setReplayPrompt($value);
                    break;

                case 'returnValue':
                    $this->setReturnValue($value);
                    break;

                case 'grayFallback':
                    $this->setGrayFallback($value);
                    break;
            }
        }

        $this->setStyles($config['styles'] ?? []);
    }

    /**
     * Enable or disable compact output.
     */
    public function setCompact(bool $compact)
    {
        $this->compact = $compact;
    }

    /**
     * Get whether to use compact output.
     */
    public function compact(): bool
    {
        return $this->compact;
    }

    /**
     * Set the prompt string.
     */
    public function setPrompt(string $prompt)
    {
        $this->prompt = $prompt;
    }

    /**
     * Get the prompt string.
     */
    public function prompt(): string
    {
        return $this->prompt;
    }

    /**
     * Set the buffer prompt string (used for multi-line input continuation).
     */
    public function setBufferPrompt(string $bufferPrompt)
    {
        $this->bufferPrompt = $bufferPrompt;
    }

    /**
     * Get the buffer prompt string (used for multi-line input continuation).
     */
    public function bufferPrompt(): string
    {
        return $this->bufferPrompt;
    }

    /**
     * Set the prompt string used when replaying history.
     */
    public function setReplayPrompt(string $replayPrompt)
    {
        $this->replayPrompt = $replayPrompt;
    }

    /**
     * Get the prompt string used when replaying history.
     */
    public function replayPrompt(): string
    {
        return $this->replayPrompt;
    }

    /**
     * Set the return value marker.
     */
    public function setReturnValue(string $returnValue)
    {
        $this->returnValue = $returnValue;
    }

    /**
     * Get the return value marker.
     */
    public function returnValue(): string
    {
        return $this->returnValue;
    }

    /**
     * Set the fallback color when "gray" is unavailable.
     */
    public function setGrayFallback(string $grayFallback)
    {
        $this->grayFallback = $grayFallback;
    }

    /**
     * Set the shell output formatter styles.
     *
     * Accepts a map from style name to [fg, bg, options], for example:
     *
     *     [
     *         'error' => ['white', 'red', ['bold']],
     *         'warning' => ['black', 'yellow'],
     *     ]
     *
     * Foreground, background or options can be null, or even omitted entirely.
     */
    public function setStyles(array $styles)
    {
        foreach (\array_keys(static::DEFAULT_STYLES) as $name) {
            $this->styles[$name] = $styles[$name] ?? static::DEFAULT_STYLES[$name];
        }
    }

    /**
     * Get the built-in theme name, or null for custom themes.
     */
    public function getName(): ?string
    {
        foreach (self::BUILTIN_THEMES as $name) {
            if ($this->equals(new self($name))) {
                return $name;
            }
        }

        return null;
    }

    /**
     * Compare two themes by effective config.
     */
    public function equals(self $theme): bool
    {
        return $this->compact === $theme->compact
            && $this->prompt === $theme->prompt
            && $this->bufferPrompt === $theme->bufferPrompt
            && $this->replayPrompt === $theme->replayPrompt
            && $this->returnValue === $theme->returnValue
            && $this->grayFallback === $theme->grayFallback
            && $this->styles === $theme->styles;
    }

    /**
     * Apply the current output formatter styles.
     */
    public function applyStyles(OutputFormatterInterface $formatter, bool $useGrayFallback)
    {
        foreach (\array_keys(static::DEFAULT_STYLES) as $name) {
            $formatter->setStyle($name, new OutputFormatterStyle(...$this->getStyle($name, $useGrayFallback)));
        }
    }

    /**
     * Checks if the "gray" color exists on the output formatter.
     */
    public static function grayExists(OutputFormatterInterface $formatter): bool
    {
        try {
            $formatter->format('<fg=gray></>');
        } catch (\InvalidArgumentException $e) {
            return false;
        }

        return true;
    }

    /**
     * Apply the current output formatter error styles.
     */
    public function applyErrorStyles(OutputFormatterInterface $errorFormatter, bool $useGrayFallback)
    {
        foreach (static::ERROR_STYLES as $name) {
            $errorFormatter->setStyle($name, new OutputFormatterStyle(...$this->getStyle($name, $useGrayFallback)));
        }
    }

    /**
     * Get a style definition as an array.
     *
     * @return array [foreground, background, options]
     */
    private function getStyle(string $name, bool $useGrayFallback): array
    {
        if (!$useGrayFallback) {
            return $this->styles[$name];
        }

        // The default input_frame styles use extended colors (bright-white,
        // gray) unavailable on older Symfony Console. Drop them rather than
        // falling back to unreadable backgrounds.
        if (($name === 'input_frame' || $name === 'input_frame_error') && $this->styles[$name] === static::DEFAULT_STYLES[$name]) {
            return [null, null];
        }

        return \array_map(fn ($style) => ($style === 'gray') ? $this->grayFallback : $style, $this->styles[$name]);
    }

    /**
     * Get a style as inline style string for use with hrefs.
     *
     * Converts style array [fg, bg, options] to inline format: "fg=color;bg=color;options=opt1,opt2"
     *
     * @return string Inline style string (e.g., "fg=blue;options=underscore")
     */
    private function getStyleAsInline(string $name, bool $useGrayFallback = false): string
    {
        $style = $this->getStyle($name, $useGrayFallback) ?? static::DEFAULT_STYLES[$name] ?? [null, null, []];
        $fg = $style[0] ?? null;
        $bg = $style[1] ?? null;
        $options = $style[2] ?? [];

        $parts = [];

        if ($fg !== null) {
            $parts[] = \sprintf('fg=%s', $fg);
        }

        if ($bg !== null) {
            $parts[] = \sprintf('bg=%s', $bg);
        }

        if (!empty($options)) {
            $parts[] = \sprintf('options=%s', \implode(',', $options));
        }

        return \implode(';', $parts);
    }

    /**
     * Get all styles as inline style strings, for use with hrefs or other manual formatting.
     *
     * @return array Map of style name to inline style string
     */
    public function getInlineStyles(bool $useGrayFallback = false): array
    {
        $inlineStyles = [];

        foreach (\array_keys(static::DEFAULT_STYLES) as $name) {
            $inlineStyles[$name] = $this->getStyleAsInline($name, $useGrayFallback);
        }

        return $inlineStyles;
    }
}
