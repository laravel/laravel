<?php

declare(strict_types=1);

namespace NunoMaduro\Collision;

use InvalidArgumentException;
use NunoMaduro\Collision\Exceptions\InvalidStyleException;
use NunoMaduro\Collision\Exceptions\ShouldNotHappen;

/**
 * @internal
 *
 * @final
 */
class ConsoleColor
{
    public const FOREGROUND = 38;

    public const BACKGROUND = 48;

    public const COLOR256_REGEXP = '~^(bg_)?color_(\d{1,3})$~';

    public const RESET_STYLE = 0;

    private bool $forceStyle = false;

    /** @var array */
    private const STYLES = [
        'none' => null,
        'bold' => '1',
        'dark' => '2',
        'italic' => '3',
        'underline' => '4',
        'blink' => '5',
        'reverse' => '7',
        'concealed' => '8',

        'default' => '39',
        'black' => '30',
        'red' => '31',
        'green' => '32',
        'yellow' => '33',
        'blue' => '34',
        'magenta' => '35',
        'cyan' => '36',
        'light_gray' => '37',

        'dark_gray' => '90',
        'light_red' => '91',
        'light_green' => '92',
        'light_yellow' => '93',
        'light_blue' => '94',
        'light_magenta' => '95',
        'light_cyan' => '96',
        'white' => '97',

        'bg_default' => '49',
        'bg_black' => '40',
        'bg_red' => '41',
        'bg_green' => '42',
        'bg_yellow' => '43',
        'bg_blue' => '44',
        'bg_magenta' => '45',
        'bg_cyan' => '46',
        'bg_light_gray' => '47',

        'bg_dark_gray' => '100',
        'bg_light_red' => '101',
        'bg_light_green' => '102',
        'bg_light_yellow' => '103',
        'bg_light_blue' => '104',
        'bg_light_magenta' => '105',
        'bg_light_cyan' => '106',
        'bg_white' => '107',
    ];

    private array $themes = [];

    /**
     * @throws InvalidStyleException
     * @throws InvalidArgumentException
     */
    public function apply(array|string $style, string $text): string
    {
        if (! $this->isStyleForced() && ! $this->isSupported()) {
            return $text;
        }

        if (is_string($style)) {
            $style = [$style];
        }
        if (! is_array($style)) {
            throw new InvalidArgumentException('Style must be string or array.');
        }

        $sequences = [];

        foreach ($style as $s) {
            // @phpstan-ignore-next-line
            if (isset($this->themes[$s])) {
                $sequences = array_merge($sequences, $this->themeSequence($s));
            } elseif ($this->isValidStyle($s)) {
                $sequences[] = $this->styleSequence($s);
            } else {
                throw new ShouldNotHappen;
            }
        }

        /** @var array<string> $filtered */
        $filtered = array_filter($sequences);

        if (empty($filtered)) {
            return $text;
        }

        return $this->escSequence(implode(';', $filtered)).$text.$this->escSequence(self::RESET_STYLE);
    }

    public function setForceStyle(bool $forceStyle): void
    {
        $this->forceStyle = $forceStyle;
    }

    public function isStyleForced(): bool
    {
        return $this->forceStyle;
    }

    public function setThemes(array $themes): void
    {
        $this->themes = [];
        foreach ($themes as $name => $styles) {
            $this->addTheme($name, $styles);
        }
    }

    public function addTheme(string $name, array|string $styles): void
    {
        if (is_string($styles)) {
            $styles = [$styles];
        }
        if (! is_array($styles)) {
            throw new InvalidArgumentException('Style must be string or array.');
        }

        foreach ($styles as $style) {
            if (! $this->isValidStyle($style)) {
                throw new InvalidStyleException($style);
            }
        }

        $this->themes[$name] = $styles;
    }

    public function getThemes(): array
    {
        return $this->themes;
    }

    public function hasTheme(string $name): bool
    {
        return isset($this->themes[$name]);
    }

    public function removeTheme(string $name): void
    {
        unset($this->themes[$name]);
    }

    public function isSupported(): bool
    {
        // The COLLISION_FORCE_COLORS variable is for internal purposes only
        if (getenv('COLLISION_FORCE_COLORS') !== false) {
            return true;
        }

        if (DIRECTORY_SEPARATOR === '\\') {
            return getenv('ANSICON') !== false || getenv('ConEmuANSI') === 'ON';
        }

        return function_exists('posix_isatty') && @posix_isatty(STDOUT);
    }

    public function are256ColorsSupported(): bool
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            return function_exists('sapi_windows_vt100_support') && @sapi_windows_vt100_support(STDOUT);
        }

        return strpos((string) getenv('TERM'), '256color') !== false;
    }

    public function getPossibleStyles(): array
    {
        return array_keys(self::STYLES);
    }

    private function themeSequence(string $name): array
    {
        $sequences = [];
        foreach ($this->themes[$name] as $style) {
            $sequences[] = $this->styleSequence($style);
        }

        return $sequences;
    }

    private function styleSequence(string $style): ?string
    {
        if (array_key_exists($style, self::STYLES)) {
            return self::STYLES[$style];
        }

        if (! $this->are256ColorsSupported()) {
            return null;
        }

        preg_match(self::COLOR256_REGEXP, $style, $matches);

        $type = ($matches[1] ?? '') === 'bg_' ? self::BACKGROUND : self::FOREGROUND;
        $value = $matches[2] ?? '0';

        return "$type;5;$value";
    }

    private function isValidStyle(string $style): bool
    {
        return array_key_exists($style, self::STYLES) || preg_match(self::COLOR256_REGEXP, $style);
    }

    private function escSequence(string|int $value): string
    {
        return "\033[{$value}m";
    }
}
