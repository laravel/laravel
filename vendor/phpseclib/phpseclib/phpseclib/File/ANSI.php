<?php

/**
 * Pure-PHP ANSI Decoder
 *
 * PHP versions 4 and 5
 *
 * If you call read() in Net_SSH2 you may get {@link http://en.wikipedia.org/wiki/ANSI_escape_code ANSI escape codes} back.
 * They'd look like chr(0x1B) . '[00m' or whatever (0x1B = ESC).  They tell a
 * {@link http://en.wikipedia.org/wiki/Terminal_emulator terminal emulator} how to format the characters, what
 * color to display them in, etc. File_ANSI is a {@link http://en.wikipedia.org/wiki/VT100 VT100} terminal emulator.
 *
 * LICENSE: Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @category  File
 * @package   File_ANSI
 * @author    Jim Wigginton <terrafrost@php.net>
 * @copyright 2012 Jim Wigginton
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link      http://phpseclib.sourceforge.net
 */

/**
 * Pure-PHP ANSI Decoder
 *
 * @package File_ANSI
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
class File_ANSI
{
    /**
     * Max Width
     *
     * @var Integer
     * @access private
     */
    var $max_x;

    /**
     * Max Height
     *
     * @var Integer
     * @access private
     */
    var $max_y;

    /**
     * Max History
     *
     * @var Integer
     * @access private
     */
    var $max_history;

    /**
     * History
     *
     * @var Array
     * @access private
     */
    var $history;

    /**
     * History Attributes
     *
     * @var Array
     * @access private
     */
    var $history_attrs;

    /**
     * Current Column
     *
     * @var Integer
     * @access private
     */
    var $x;

    /**
     * Current Row
     *
     * @var Integer
     * @access private
     */
    var $y;

    /**
     * Old Column
     *
     * @var Integer
     * @access private
     */
    var $old_x;

    /**
     * Old Row
     *
     * @var Integer
     * @access private
     */
    var $old_y;

    /**
     * An empty attribute row
     *
     * @var Array
     * @access private
     */
    var $attr_row;

    /**
     * The current screen text
     *
     * @var Array
     * @access private
     */
    var $screen;

    /**
     * The current screen attributes
     *
     * @var Array
     * @access private
     */
    var $attrs;

    /**
     * The current foreground color
     *
     * @var String
     * @access private
     */
    var $foreground;

    /**
     * The current background color
     *
     * @var String
     * @access private
     */
    var $background;

    /**
     * Bold flag
     *
     * @var Boolean
     * @access private
     */
    var $bold;

    /**
     * Underline flag
     *
     * @var Boolean
     * @access private
     */
    var $underline;

    /**
     * Blink flag
     *
     * @var Boolean
     * @access private
     */
    var $blink;

    /**
     * Reverse flag
     *
     * @var Boolean
     * @access private
     */
    var $reverse;

    /**
     * Color flag
     *
     * @var Boolean
     * @access private
     */
    var $color;

    /**
     * Current ANSI code
     *
     * @var String
     * @access private
     */
    var $ansi;

    /**
     * Default Constructor.
     *
     * @return File_ANSI
     * @access public
     */
    function File_ANSI()
    {
        $this->setHistory(200);
        $this->setDimensions(80, 24);
    }

    /**
     * Set terminal width and height
     *
     * Resets the screen as well
     *
     * @param Integer $x
     * @param Integer $y
     * @access public
     */
    function setDimensions($x, $y)
    {
        $this->max_x = $x - 1;
        $this->max_y = $y - 1;
        $this->x = $this->y = 0;
        $this->history = $this->history_attrs = array();
        $this->attr_row = array_fill(0, $this->max_x + 1, '');
        $this->screen = array_fill(0, $this->max_y + 1, '');
        $this->attrs = array_fill(0, $this->max_y + 1, $this->attr_row);
        $this->foreground = 'white';
        $this->background = 'black';
        $this->bold = false;
        $this->underline = false;
        $this->blink = false;
        $this->reverse = false;
        $this->color = false;

        $this->ansi = '';
    }

    /**
     * Set the number of lines that should be logged past the terminal height
     *
     * @param Integer $x
     * @param Integer $y
     * @access public
     */
    function setHistory($history)
    {
        $this->max_history = $history;
    }

    /**
     * Load a string
     *
     * @param String $source
     * @access public
     */
    function loadString($source)
    {
        $this->setDimensions($this->max_x + 1, $this->max_y + 1);
        $this->appendString($source);
    }

    /**
     * Appdend a string
     *
     * @param String $source
     * @access public
     */
    function appendString($source)
    {
        for ($i = 0; $i < strlen($source); $i++) {
            if (strlen($this->ansi)) {
                $this->ansi.= $source[$i];
                $chr = ord($source[$i]);
                // http://en.wikipedia.org/wiki/ANSI_escape_code#Sequence_elements
                // single character CSI's not currently supported
                switch (true) {
                    case $this->ansi == "\x1B=":
                        $this->ansi = '';
                        continue 2;
                    case strlen($this->ansi) == 2 && $chr >= 64 && $chr <= 95 && $chr != ord('['):
                    case strlen($this->ansi) > 2 && $chr >= 64 && $chr <= 126:
                        break;
                    default:
                        continue 2;
                }
                // http://ascii-table.com/ansi-escape-sequences-vt-100.php
                switch ($this->ansi) {
                    case "\x1B[H": // Move cursor to upper left corner
                        $this->old_x = $this->x;
                        $this->old_y = $this->y;
                        $this->x = $this->y = 0;
                        break;
                    case "\x1B[J": // Clear screen from cursor down
                        $this->history = array_merge($this->history, array_slice(array_splice($this->screen, $this->y + 1), 0, $this->old_y));
                        $this->screen = array_merge($this->screen, array_fill($this->y, $this->max_y, ''));

                        $this->history_attrs = array_merge($this->history_attrs, array_slice(array_splice($this->attrs, $this->y + 1), 0, $this->old_y));
                        $this->attrs = array_merge($this->attrs, array_fill($this->y, $this->max_y, $this->attr_row));

                        if (count($this->history) == $this->max_history) {
                            array_shift($this->history);
                            array_shift($this->history_attrs);
                        }
                    case "\x1B[K": // Clear screen from cursor right
                        $this->screen[$this->y] = substr($this->screen[$this->y], 0, $this->x);

                        array_splice($this->attrs[$this->y], $this->x + 1);
                        break;
                    case "\x1B[2K": // Clear entire line
                        $this->screen[$this->y] = str_repeat(' ', $this->x);
                        $this->attrs[$this->y] = $this->attr_row;
                        break;
                    case "\x1B[?1h": // set cursor key to application
                    case "\x1B[?25h": // show the cursor
                        break;
                    case "\x1BE": // Move to next line
                        $this->_newLine();
                        $this->x = 0;
                        break;
                    default:
                        switch (true) {
                            case preg_match('#\x1B\[(\d+);(\d+)H#', $this->ansi, $match): // Move cursor to screen location v,h
                                $this->old_x = $this->x;
                                $this->old_y = $this->y;
                                $this->x = $match[2] - 1;
                                $this->y = $match[1] - 1;
                                break;
                            case preg_match('#\x1B\[(\d+)C#', $this->ansi, $match): // Move cursor right n lines
                                $this->old_x = $this->x;
                                $x = $match[1] - 1;
                                break;
                            case preg_match('#\x1B\[(\d+);(\d+)r#', $this->ansi, $match): // Set top and bottom lines of a window
                                break;
                            case preg_match('#\x1B\[(\d*(?:;\d*)*)m#', $this->ansi, $match): // character attributes
                                $mods = explode(';', $match[1]);
                                foreach ($mods as $mod) {
                                    switch ($mod) {
                                        case 0: // Turn off character attributes
                                            $this->attrs[$this->y][$this->x] = '';

                                            if ($this->bold) $this->attrs[$this->y][$this->x].= '</b>';
                                            if ($this->underline) $this->attrs[$this->y][$this->x].= '</u>';
                                            if ($this->blink) $this->attrs[$this->y][$this->x].= '</blink>';
                                            if ($this->color) $this->attrs[$this->y][$this->x].= '</span>';

                                            if ($this->reverse) {
                                                $temp = $this->background;
                                                $this->background = $this->foreground;
                                                $this->foreground = $temp;
                                            }

                                            $this->bold = $this->underline = $this->blink = $this->color = $this->reverse = false;
                                            break;
                                        case 1: // Turn bold mode on
                                            if (!$this->bold) {
                                                $this->attrs[$this->y][$this->x] = '<b>';
                                                $this->bold = true;
                                            }
                                            break;
                                        case 4: // Turn underline mode on
                                            if (!$this->underline) {
                                                $this->attrs[$this->y][$this->x] = '<u>';
                                                $this->underline = true;
                                            }
                                            break;
                                        case 5: // Turn blinking mode on
                                            if (!$this->blink) {
                                                $this->attrs[$this->y][$this->x] = '<blink>';
                                                $this->blink = true;
                                            }
                                            break;
                                        case 7: // Turn reverse video on
                                            $this->reverse = !$this->reverse;
                                            $temp = $this->background;
                                            $this->background = $this->foreground;
                                            $this->foreground = $temp;
                                            $this->attrs[$this->y][$this->x] = '<span style="color: ' . $this->foreground . '; background: ' . $this->background . '">';
                                            if ($this->color) {
                                                $this->attrs[$this->y][$this->x] = '</span>' . $this->attrs[$this->y][$this->x];
                                            }
                                            $this->color = true;
                                            break;
                                        default: // set colors
                                            //$front = $this->reverse ? &$this->background : &$this->foreground;
                                            $front = &$this->{ $this->reverse ? 'background' : 'foreground' };
                                            //$back = $this->reverse ? &$this->foreground : &$this->background;
                                            $back = &$this->{ $this->reverse ? 'foreground' : 'background' };
                                            switch ($mod) {
                                                case 30: $front = 'black'; break;
                                                case 31: $front = 'red'; break;
                                                case 32: $front = 'green'; break;
                                                case 33: $front = 'yellow'; break;
                                                case 34: $front = 'blue'; break;
                                                case 35: $front = 'magenta'; break;
                                                case 36: $front = 'cyan'; break;
                                                case 37: $front = 'white'; break;

                                                case 40: $back = 'black'; break;
                                                case 41: $back = 'red'; break;
                                                case 42: $back = 'green'; break;
                                                case 43: $back = 'yellow'; break;
                                                case 44: $back = 'blue'; break;
                                                case 45: $back = 'magenta'; break;
                                                case 46: $back = 'cyan'; break;
                                                case 47: $back = 'white'; break;

                                                default:
                                                    user_error('Unsupported attribute: ' . $mod);
                                                    $this->ansi = '';
                                                    break 2;
                                            }

                                            unset($temp);
                                            $this->attrs[$this->y][$this->x] = '<span style="color: ' . $this->foreground . '; background: ' . $this->background . '">';
                                            if ($this->color) {
                                                $this->attrs[$this->y][$this->x] = '</span>' . $this->attrs[$this->y][$this->x];
                                            }
                                            $this->color = true;
                                    }
                                }
                                break;
                            default:
                                user_error("{$this->ansi} unsupported\r\n");
                        }
                }
                $this->ansi = '';
                continue;
            }

            switch ($source[$i]) {
                case "\r":
                    $this->x = 0;
                    break;
                case "\n":
                    $this->_newLine();
                    break;
                case "\x0F": // shift
                    break;
                case "\x1B": // start ANSI escape code
                    $this->ansi.= "\x1B";
                    break;
                default:
                    $this->screen[$this->y] = substr_replace(
                        $this->screen[$this->y],
                        $source[$i],
                        $this->x,
                        1
                    );

                    if ($this->x > $this->max_x) {
                        $this->x = 0;
                        $this->y++;
                    } else {
                        $this->x++;
                    }
            }
        }
    }

    /**
     * Add a new line
     *
     * Also update the $this->screen and $this->history buffers
     *
     * @access private
     */
    function _newLine()
    {
        //if ($this->y < $this->max_y) {
        //    $this->y++;
        //}

        while ($this->y >= $this->max_y) {
            $this->history = array_merge($this->history, array(array_shift($this->screen)));
            $this->screen[] = '';

            $this->history_attrs = array_merge($this->history_attrs, array(array_shift($this->attrs)));
            $this->attrs[] = $this->attr_row;

            if (count($this->history) >= $this->max_history) {
                array_shift($this->history);
                array_shift($this->history_attrs);
            }

            $this->y--;
        }
        $this->y++;
    }

    /**
     * Returns the current screen without preformating
     *
     * @access private
     * @return String
     */
    function _getScreen()
    {
        $output = '';
        for ($i = 0; $i <= $this->max_y; $i++) {
            for ($j = 0; $j <= $this->max_x + 1; $j++) {
                if (isset($this->attrs[$i][$j])) {
                    $output.= $this->attrs[$i][$j];
                }
                if (isset($this->screen[$i][$j])) {
                    $output.= htmlspecialchars($this->screen[$i][$j]);
                }
            }
            $output.= "\r\n";
        }
        return rtrim($output);
    }

    /**
     * Returns the current screen
     *
     * @access public
     * @return String
     */
    function getScreen()
    {
        return '<pre style="color: white; background: black" width="' . ($this->max_x + 1) . '">' . $this->_getScreen() . '</pre>';
    }

    /**
     * Returns the current screen and the x previous lines
     *
     * @access public
     * @return String
     */
    function getHistory()
    {
        $scrollback = '';
        for ($i = 0; $i < count($this->history); $i++) {
            for ($j = 0; $j <= $this->max_x + 1; $j++) {
                if (isset($this->history_attrs[$i][$j])) {
                    $scrollback.= $this->history_attrs[$i][$j];
                }
                if (isset($this->history[$i][$j])) {
                    $scrollback.= htmlspecialchars($this->history[$i][$j]);
                }
            }
            $scrollback.= "\r\n";
        }
        $scrollback.= $this->_getScreen();

        return '<pre style="color: white; background: black" width="' . ($this->max_x + 1) . '">' . $scrollback . '</pre>';
    }
}
