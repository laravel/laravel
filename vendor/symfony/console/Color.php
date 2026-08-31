<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console;

use Symfony\Component\Console\Exception\InvalidArgumentException;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class Color
{
    private const COLORS = [
        'black' => 0,
        'red' => 1,
        'green' => 2,
        'yellow' => 3,
        'blue' => 4,
        'magenta' => 5,
        'cyan' => 6,
        'white' => 7,
        'default' => 9,
    ];

    private const BRIGHT_COLORS = [
        'gray' => 0,
        'bright-red' => 1,
        'bright-green' => 2,
        'bright-yellow' => 3,
        'bright-blue' => 4,
        'bright-magenta' => 5,
        'bright-cyan' => 6,
        'bright-white' => 7,
    ];

    private const AVAILABLE_OPTIONS = [
        'bold' => ['set' => 1, 'unset' => 22],
        'underscore' => ['set' => 4, 'unset' => 24],
        'blink' => ['set' => 5, 'unset' => 25],
        'reverse' => ['set' => 7, 'unset' => 27],
        'conceal' => ['set' => 8, 'unset' => 28],
    ];

    private string $foreground;
    private string $background;
    private array $options = [];

    public function __construct(string $foreground = '', string $background = '', array $options = [])
    {
        $this->foreground = $this->parseColor($foreground);
        $this->background = $this->parseColor($background, true);

        foreach ($options as $option) {
            if (!isset(self::AVAILABLE_OPTIONS[$option])) {
                throw new InvalidArgumentException(\sprintf('Invalid option specified: "%s". Expected one of (%s).', $option, implode(', ', array_keys(self::AVAILABLE_OPTIONS))));
            }

            $this->options[$option] = self::AVAILABLE_OPTIONS[$option];
        }
    }

    public function apply(string $text): string
    {
        return $this->set().$text.$this->unset();
    }

    public function set(): string
    {
        $setCodes = [];
        if ('' !== $this->foreground) {
            $setCodes[] = $this->foreground;
        }
        if ('' !== $this->background) {
            $setCodes[] = $this->background;
        }
        foreach ($this->options as $option) {
            $setCodes[] = $option['set'];
        }
        if (0 === \count($setCodes)) {
            return '';
        }

        return \sprintf("\033[%sm", implode(';', $setCodes));
    }

    public function unset(): string
    {
        $unsetCodes = [];
        if ('' !== $this->foreground) {
            $unsetCodes[] = 39;
        }
        if ('' !== $this->background) {
            $unsetCodes[] = 49;
        }
        foreach ($this->options as $option) {
            $unsetCodes[] = $option['unset'];
        }
        if (0 === \count($unsetCodes)) {
            return '';
        }

        return \sprintf("\033[%sm", implode(';', $unsetCodes));
    }

    private function parseColor(string $color, bool $background = false): string
    {
        if ('' === $color) {
            return '';
        }

        if ('#' === $color[0]) {
            return ($background ? '4' : '3').Terminal::getColorMode()->convertFromHexToAnsiColorCode($color);
        }

        if (isset(self::COLORS[$color])) {
            return ($background ? '4' : '3').self::COLORS[$color];
        }

        if (isset(self::BRIGHT_COLORS[$color])) {
            return ($background ? '10' : '9').self::BRIGHT_COLORS[$color];
        }

        throw new InvalidArgumentException(\sprintf('Invalid "%s" color; expected one of (%s).', $color, implode(', ', array_merge(array_keys(self::COLORS), array_keys(self::BRIGHT_COLORS)))));
    }
}
