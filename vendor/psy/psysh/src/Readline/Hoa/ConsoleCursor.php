<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2017, Hoa community. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace Psy\Readline\Hoa;

/**
 * Class \Hoa\Console\Cursor.
 *
 * Allow to manipulate the cursor.
 */
class ConsoleCursor
{
    /**
     * Move the cursor.
     * Steps can be:
     *     • u, up,    ↑ : move to the previous line;
     *     • U, UP       : move to the first line;
     *     • r, right, → : move to the next column;
     *     • R, RIGHT    : move to the last column;
     *     • d, down,  ↓ : move to the next line;
     *     • D, DOWN     : move to the last line;
     *     • l, left,  ← : move to the previous column;
     *     • L, LEFT     : move to the first column.
     * Steps can be concatened by a single space if $repeat is equal to 1.
     */
    public static function move(string $steps, int $repeat = 1)
    {
        if (1 > $repeat) {
            return;
        } elseif (1 === $repeat) {
            $handle = \explode(' ', $steps);
        } else {
            $handle = \explode(' ', $steps, 1);
        }

        $tput = Console::getTput();
        $output = Console::getOutput();

        foreach ($handle as $step) {
            switch ($step) {
                case 'u':
                case 'up':
                case '↑':
                    $output->writeAll(
                        \str_replace(
                            '%p1%d',
                            $repeat,
                            $tput->get('parm_up_cursor')
                        )
                    );

                    break;

                case 'U':
                case 'UP':
                    static::moveTo(null, 1);

                    break;

                case 'r':
                case 'right':
                case '→':
                    $output->writeAll(
                        \str_replace(
                            '%p1%d',
                            $repeat,
                            $tput->get('parm_right_cursor')
                        )
                    );

                    break;

                case 'R':
                case 'RIGHT':
                    static::moveTo(9999);

                    break;

                case 'd':
                case 'down':
                case '↓':
                    $output->writeAll(
                        \str_replace(
                            '%p1%d',
                            $repeat,
                            $tput->get('parm_down_cursor')
                        )
                    );

                    break;

                case 'D':
                case 'DOWN':
                    static::moveTo(null, 9999);

                    break;

                case 'l':
                case 'left':
                case '←':
                    $output->writeAll(
                        \str_replace(
                            '%p1%d',
                            $repeat,
                            $tput->get('parm_left_cursor')
                        )
                    );

                    break;

                case 'L':
                case 'LEFT':
                    static::moveTo(1);

                    break;
            }
        }
    }

    /**
     * Move to the line X and the column Y.
     * If null, use the current coordinate.
     */
    public static function moveTo(?int $x = null, ?int $y = null)
    {
        if (null === $x || null === $y) {
            $position = static::getPosition();

            if (null === $x) {
                $x = $position['x'];
            }

            if (null === $y) {
                $y = $position['y'];
            }
        }

        Console::getOutput()->writeAll(
            \str_replace(
                ['%i%p1%d', '%p2%d'],
                [$y, $x],
                Console::getTput()->get('cursor_address')
            )
        );
    }

    /**
     * Get current position (x and y) of the cursor.
     */
    public static function getPosition(): array
    {
        $tput = Console::getTput();
        $user7 = $tput->get('user7');

        if (null === $user7) {
            return [
                'x' => 0,
                'y' => 0,
            ];
        }

        Console::getOutput()->writeAll($user7);

        $input = Console::getInput();

        // Read $tput->get('user6').
        $input->read(2); // skip \033 and [.

        $x = null;
        $y = null;
        $handle = &$y;

        while (true) {
            $char = $input->readCharacter();

            switch ($char) {
                case ';':
                    $handle = &$x;

                    break;

                case 'R':
                    break 2;

                default:
                    $handle .= $char;
            }
        }

        return [
            'x' => (int) $x,
            'y' => (int) $y,
        ];
    }

    /**
     * Save current position.
     */
    public static function save()
    {
        Console::getOutput()->writeAll(
            Console::getTput()->get('save_cursor')
        );
    }

    /**
     * Restore cursor to the last saved position.
     */
    public static function restore()
    {
        Console::getOutput()->writeAll(
            Console::getTput()->get('restore_cursor')
        );
    }

    /**
     * Clear the screen.
     * Part can be:
     *     • a, all,   ↕ : clear entire screen and static::move(1, 1);
     *     • u, up,    ↑ : clear from cursor to beginning of the screen;
     *     • r, right, → : clear from cursor to the end of the line;
     *     • d, down,  ↓ : clear from cursor to end of the screen;
     *     • l, left,  ← : clear from cursor to beginning of the screen;
     *     •    line,  ↔ : clear all the line and static::move(1).
     * Parts can be concatenated by a single space.
     */
    public static function clear(string $parts = 'all')
    {
        $tput = Console::getTput();
        $output = Console::getOutput();

        foreach (\explode(' ', $parts) as $part) {
            switch ($part) {
                case 'a':
                case 'all':
                case '↕':
                    $output->writeAll($tput->get('clear_screen'));
                    static::moveTo(1, 1);

                    break;

                case 'u':
                case 'up':
                case '↑':
                    $output->writeAll("\033[1J");

                    break;

                case 'r':
                case 'right':
                case '→':
                    $output->writeAll($tput->get('clr_eol'));

                    break;

                case 'd':
                case 'down':
                case '↓':
                    $output->writeAll($tput->get('clr_eos'));

                    break;

                case 'l':
                case 'left':
                case '←':
                    $output->writeAll($tput->get('clr_bol'));

                    break;

                case 'line':
                case '↔':
                    $output->writeAll("\r".$tput->get('clr_eol'));

                    break;
            }
        }
    }

    /**
     * Hide the cursor.
     */
    public static function hide()
    {
        Console::getOutput()->writeAll(
            Console::getTput()->get('cursor_invisible')
        );
    }

    /**
     * Show the cursor.
     */
    public static function show()
    {
        Console::getOutput()->writeAll(
            Console::getTput()->get('cursor_visible')
        );
    }

    /**
     * Colorize cursor.
     * Attributes can be:
     *     •  n,         normal           : normal;
     *     •  b,         bold             : bold;
     *     •  u,         underlined       : underlined;
     *     •  bl,        blink            : blink;
     *     •  i,         inverse          : inverse;
     *     • !b,        !bold             : normal weight;
     *     • !u,        !underlined       : not underlined;
     *     • !bl,       !blink            : steady;
     *     • !i,        !inverse          : positive;
     *     • fg(color), foreground(color) : set foreground to “color”;
     *     • bg(color), background(color) : set background to “color”.
     * “color” can be:
     *     • default;
     *     • black;
     *     • red;
     *     • green;
     *     • yellow;
     *     • blue;
     *     • magenta;
     *     • cyan;
     *     • white;
     *     • 0-256 (classic palette);
     *     • #hexa.
     * Attributes can be concatenated by a single space.
     */
    public static function colorize(string $attributes)
    {
        static $_rgbTo256 = null;

        if (null === $_rgbTo256) {
            $_rgbTo256 = [
                '000000', '800000', '008000', '808000', '000080', '800080',
                '008080', 'c0c0c0', '808080', 'ff0000', '00ff00', 'ffff00',
                '0000ff', 'ff00ff', '00ffff', 'ffffff', '000000', '00005f',
                '000087', '0000af', '0000d7', '0000ff', '005f00', '005f5f',
                '005f87', '005faf', '005fd7', '005fff', '008700', '00875f',
                '008787', '0087af', '0087d7', '0087ff', '00af00', '00af5f',
                '00af87', '00afaf', '00afd7', '00afff', '00d700', '00d75f',
                '00d787', '00d7af', '00d7d7', '00d7ff', '00ff00', '00ff5f',
                '00ff87', '00ffaf', '00ffd7', '00ffff', '5f0000', '5f005f',
                '5f0087', '5f00af', '5f00d7', '5f00ff', '5f5f00', '5f5f5f',
                '5f5f87', '5f5faf', '5f5fd7', '5f5fff', '5f8700', '5f875f',
                '5f8787', '5f87af', '5f87d7', '5f87ff', '5faf00', '5faf5f',
                '5faf87', '5fafaf', '5fafd7', '5fafff', '5fd700', '5fd75f',
                '5fd787', '5fd7af', '5fd7d7', '5fd7ff', '5fff00', '5fff5f',
                '5fff87', '5fffaf', '5fffd7', '5fffff', '870000', '87005f',
                '870087', '8700af', '8700d7', '8700ff', '875f00', '875f5f',
                '875f87', '875faf', '875fd7', '875fff', '878700', '87875f',
                '878787', '8787af', '8787d7', '8787ff', '87af00', '87af5f',
                '87af87', '87afaf', '87afd7', '87afff', '87d700', '87d75f',
                '87d787', '87d7af', '87d7d7', '87d7ff', '87ff00', '87ff5f',
                '87ff87', '87ffaf', '87ffd7', '87ffff', 'af0000', 'af005f',
                'af0087', 'af00af', 'af00d7', 'af00ff', 'af5f00', 'af5f5f',
                'af5f87', 'af5faf', 'af5fd7', 'af5fff', 'af8700', 'af875f',
                'af8787', 'af87af', 'af87d7', 'af87ff', 'afaf00', 'afaf5f',
                'afaf87', 'afafaf', 'afafd7', 'afafff', 'afd700', 'afd75f',
                'afd787', 'afd7af', 'afd7d7', 'afd7ff', 'afff00', 'afff5f',
                'afff87', 'afffaf', 'afffd7', 'afffff', 'd70000', 'd7005f',
                'd70087', 'd700af', 'd700d7', 'd700ff', 'd75f00', 'd75f5f',
                'd75f87', 'd75faf', 'd75fd7', 'd75fff', 'd78700', 'd7875f',
                'd78787', 'd787af', 'd787d7', 'd787ff', 'd7af00', 'd7af5f',
                'd7af87', 'd7afaf', 'd7afd7', 'd7afff', 'd7d700', 'd7d75f',
                'd7d787', 'd7d7af', 'd7d7d7', 'd7d7ff', 'd7ff00', 'd7ff5f',
                'd7ff87', 'd7ffaf', 'd7ffd7', 'd7ffff', 'ff0000', 'ff005f',
                'ff0087', 'ff00af', 'ff00d7', 'ff00ff', 'ff5f00', 'ff5f5f',
                'ff5f87', 'ff5faf', 'ff5fd7', 'ff5fff', 'ff8700', 'ff875f',
                'ff8787', 'ff87af', 'ff87d7', 'ff87ff', 'ffaf00', 'ffaf5f',
                'ffaf87', 'ffafaf', 'ffafd7', 'ffafff', 'ffd700', 'ffd75f',
                'ffd787', 'ffd7af', 'ffd7d7', 'ffd7ff', 'ffff00', 'ffff5f',
                'ffff87', 'ffffaf', 'ffffd7', 'ffffff', '080808', '121212',
                '1c1c1c', '262626', '303030', '3a3a3a', '444444', '4e4e4e',
                '585858', '606060', '666666', '767676', '808080', '8a8a8a',
                '949494', '9e9e9e', 'a8a8a8', 'b2b2b2', 'bcbcbc', 'c6c6c6',
                'd0d0d0', 'dadada', 'e4e4e4', 'eeeeee',
            ];
        }

        $tput = Console::getTput();

        if (1 >= $tput->count('max_colors')) {
            return;
        }

        $handle = [];

        foreach (\explode(' ', $attributes) as $attribute) {
            switch ($attribute) {
                case 'n':
                case 'normal':
                    $handle[] = 0;

                    break;

                case 'b':
                case 'bold':
                    $handle[] = 1;

                    break;

                case 'u':
                case 'underlined':
                    $handle[] = 4;

                    break;

                case 'bl':
                case 'blink':
                    $handle[] = 5;

                    break;

                case 'i':
                case 'inverse':
                    $handle[] = 7;

                    break;

                case '!b':
                case '!bold':
                    $handle[] = 22;

                    break;

                case '!u':
                case '!underlined':
                    $handle[] = 24;

                    break;

                case '!bl':
                case '!blink':
                    $handle[] = 25;

                    break;

                case '!i':
                case '!inverse':
                    $handle[] = 27;

                    break;

                default:
                    if (0 === \preg_match('#^([^\(]+)\(([^\)]+)\)$#', $attribute, $m)) {
                        break;
                    }

                    $shift = 0;

                    switch ($m[1]) {
                        case 'fg':
                        case 'foreground':
                            $shift = 0;

                            break;

                        case 'bg':
                        case 'background':
                            $shift = 10;

                            break;

                        default:
                            break 2;
                    }

                    $_handle = 0;
                    $_keyword = true;

                    switch ($m[2]) {
                        case 'black':
                            $_handle = 30;

                            break;

                        case 'red':
                            $_handle = 31;

                            break;

                        case 'green':
                            $_handle = 32;

                            break;

                        case 'yellow':
                            $_handle = 33;

                            break;

                        case 'blue':
                            $_handle = 34;

                            break;

                        case 'magenta':
                            $_handle = 35;

                            break;

                        case 'cyan':
                            $_handle = 36;

                            break;

                        case 'white':
                            $_handle = 37;

                            break;

                        case 'default':
                            $_handle = 39;

                            break;

                        default:
                            $_keyword = false;

                            if (256 <= $tput->count('max_colors') &&
                                '#' === $m[2][0]) {
                                $rgb = \hexdec(\substr($m[2], 1));
                                $r = ($rgb >> 16) & 255;
                                $g = ($rgb >> 8) & 255;
                                $b = $rgb & 255;
                                $distance = null;

                                foreach ($_rgbTo256 as $i => $_rgb) {
                                    $_rgb = \hexdec($_rgb);
                                    $_r = ($_rgb >> 16) & 255;
                                    $_g = ($_rgb >> 8) & 255;
                                    $_b = $_rgb & 255;

                                    $d = \sqrt(
                                        ($_r - $r) ** 2
                                      + ($_g - $g) ** 2
                                      + ($_b - $b) ** 2
                                    );

                                    if (null === $distance ||
                                        $d <= $distance) {
                                        $distance = $d;
                                        $_handle = $i;
                                    }
                                }
                            } else {
                                $_handle = (int) ($m[2]);
                            }
                    }

                    if (true === $_keyword) {
                        $handle[] = $_handle + $shift;
                    } else {
                        $handle[] = (38 + $shift).';5;'.$_handle;
                    }
            }
        }

        Console::getOutput()->writeAll("\033[".\implode(';', $handle).'m');

        return;
    }

    /**
     * Change color number to a specific RGB color.
     */
    public static function changeColor(int $fromCode, int $toColor)
    {
        $tput = Console::getTput();

        if (true !== $tput->has('can_change')) {
            return;
        }

        $r = ($toColor >> 16) & 255;
        $g = ($toColor >> 8) & 255;
        $b = $toColor & 255;

        Console::getOutput()->writeAll(
            \str_replace(
                [
                    '%p1%d',
                    'rgb:',
                    '%p2%{255}%*%{1000}%/%2.2X/',
                    '%p3%{255}%*%{1000}%/%2.2X/',
                    '%p4%{255}%*%{1000}%/%2.2X',
                ],
                [
                    $fromCode,
                    '',
                    \sprintf('%02x', $r),
                    \sprintf('%02x', $g),
                    \sprintf('%02x', $b),
                ],
                $tput->get('initialize_color')
            )
        );

        return;
    }

    /**
     * Set cursor style.
     * Style can be:
     *     • b, block,     ▋: block;
     *     • u, underline, _: underline;
     *     • v, vertical,  |: vertical.
     */
    public static function setStyle(string $style, bool $blink = true)
    {
        if (\defined('PHP_WINDOWS_VERSION_PLATFORM')) {
            return;
        }

        switch ($style) {
            case 'u':
            case 'underline':
            case '_':
                $_style = 2;

                break;

            case 'v':
            case 'vertical':
            case '|':
                $_style = 5;

                break;

            case 'b':
            case 'block':
            case '▋':
            default:
                $_style = 1;

                break;
        }

        if (false === $blink) {
            ++$_style;
        }

        // Not sure what tput entry we can use here…
        Console::getOutput()->writeAll("\033[".$_style.' q');

        return;
    }

    /**
     * Make a stupid “bip”.
     */
    public static function bip()
    {
        Console::getOutput()->writeAll(
            Console::getTput()->get('bell')
        );
    }
}

/*
 * Advanced interaction.
 */
Console::advancedInteraction();
