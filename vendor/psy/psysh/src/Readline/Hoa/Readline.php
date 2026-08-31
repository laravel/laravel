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
 * Class \Hoa\Console\Readline.
 *
 * Read, edit, bind… a line from the input.
 */
class Readline
{
    /**
     * State: continue to read.
     */
    const STATE_CONTINUE = 1;

    /**
     * State: stop to read.
     */
    const STATE_BREAK = 2;

    /**
     * State: no output the current buffer.
     */
    const STATE_NO_ECHO = 4;

    /**
     * Current editing line.
     */
    protected $_line = null;

    /**
     * Current editing line seek.
     */
    protected $_lineCurrent = 0;

    /**
     * Current editing line length.
     */
    protected $_lineLength = 0;

    /**
     * Current buffer (most of the time, a char).
     */
    protected $_buffer = null;

    /**
     * Mapping.
     */
    protected $_mapping = [];

    /**
     * History.
     */
    protected $_history = [];

    /**
     * History current position.
     */
    protected $_historyCurrent = 0;

    /**
     * History size.
     */
    protected $_historySize = 0;

    /**
     * Prefix.
     */
    protected $_prefix = null;

    /**
     * Autocompleter.
     */
    protected $_autocompleter = null;

    /**
     * Initialize the readline editor.
     */
    public function __construct()
    {
        if (\defined('PHP_WINDOWS_VERSION_PLATFORM')) {
            return;
        }

        $this->_mapping["\033[A"] = [$this, '_bindArrowUp'];
        $this->_mapping["\033[B"] = [$this, '_bindArrowDown'];
        $this->_mapping["\033[C"] = [$this, '_bindArrowRight'];
        $this->_mapping["\033[D"] = [$this, '_bindArrowLeft'];
        $this->_mapping["\001"] = [$this, '_bindControlA'];
        $this->_mapping["\002"] = [$this, '_bindControlB'];
        $this->_mapping["\005"] = [$this, '_bindControlE'];
        $this->_mapping["\006"] = [$this, '_bindControlF'];
        $this->_mapping["\010"] =
        $this->_mapping["\177"] = [$this, '_bindBackspace'];
        $this->_mapping["\027"] = [$this, '_bindControlW'];
        $this->_mapping["\n"] = [$this, '_bindNewline'];
        $this->_mapping["\t"] = [$this, '_bindTab'];

        return;
    }

    /**
     * Read a line from the input.
     */
    public function readLine(?string $prefix = null)
    {
        $input = Console::getInput();

        if (true === $input->eof()) {
            return false;
        }

        $direct = Console::isDirect($input->getStream()->getStream());
        $output = Console::getOutput();

        if (false === $direct || \defined('PHP_WINDOWS_VERSION_PLATFORM')) {
            $out = $input->readLine();

            if (false === $out) {
                return false;
            }

            $out = \substr($out, 0, -1);

            if (true === $direct) {
                $output->writeAll($prefix);
            } else {
                $output->writeAll($prefix.$out."\n");
            }

            return $out;
        }

        $this->resetLine();
        $this->setPrefix($prefix);
        $read = [$input->getStream()->getStream()];
        $write = $except = [];
        $output->writeAll($prefix);

        while (true) {
            @\stream_select($read, $write, $except, 30, 0);

            if (empty($read)) {
                $read = [$input->getStream()->getStream()];

                continue;
            }

            $char = $this->_read();
            $this->_buffer = $char;
            $return = $this->_readLine($char);

            if (0 === ($return & self::STATE_NO_ECHO)) {
                $output->writeAll($this->_buffer);
            }

            if (0 !== ($return & self::STATE_BREAK)) {
                break;
            }
        }

        return $this->getLine();
    }

    /**
     * Readline core.
     */
    public function _readLine(string $char)
    {
        if (isset($this->_mapping[$char]) &&
            \is_callable($this->_mapping[$char])) {
            $mapping = $this->_mapping[$char];

            return $mapping($this);
        }

        if (isset($this->_mapping[$char])) {
            $this->_buffer = $this->_mapping[$char];
        } elseif (false === Ustring::isCharPrintable($char)) {
            ConsoleCursor::bip();

            return static::STATE_CONTINUE | static::STATE_NO_ECHO;
        }

        if ($this->getLineLength() === $this->getLineCurrent()) {
            $this->appendLine($this->_buffer);

            return static::STATE_CONTINUE;
        }

        $this->insertLine($this->_buffer);
        $tail = \mb_substr(
            $this->getLine(),
            $this->getLineCurrent() - 1
        );
        $this->_buffer = "\033[K".$tail.\str_repeat(
            "\033[D",
            \mb_strlen($tail) - 1
        );

        return static::STATE_CONTINUE;
    }

    /**
     * Add mappings.
     */
    public function addMappings(array $mappings)
    {
        foreach ($mappings as $key => $mapping) {
            $this->addMapping($key, $mapping);
        }
    }

    /**
     * Add a mapping.
     * Supported key:
     *     • \e[… for \033[…;
     *     • \C-… for Ctrl-…;
     *     • abc for a simple mapping.
     * A mapping is a callable that has only one parameter of type
     * Hoa\Console\Readline and that returns a self::STATE_* constant.
     */
    public function addMapping(string $key, $mapping)
    {
        if ('\e[' === \substr($key, 0, 3)) {
            $this->_mapping["\033[".\substr($key, 3)] = $mapping;
        } elseif ('\C-' === \substr($key, 0, 3)) {
            $_key = \ord(\strtolower(\substr($key, 3))) - 96;
            $this->_mapping[\chr($_key)] = $mapping;
        } else {
            $this->_mapping[$key] = $mapping;
        }
    }

    /**
     * Add an entry in the history.
     */
    public function addHistory(?string $line = null)
    {
        if (empty($line)) {
            return;
        }

        $this->_history[] = $line;
        $this->_historyCurrent = $this->_historySize++;
    }

    /**
     * Clear history.
     */
    public function clearHistory()
    {
        unset($this->_history);
        $this->_history = [];
        $this->_historyCurrent = 0;
        $this->_historySize = 1;
    }

    /**
     * Get an entry in the history.
     */
    public function getHistory(?int $i = null)
    {
        if (null === $i) {
            $i = $this->_historyCurrent;
        }

        if (!isset($this->_history[$i])) {
            return null;
        }

        return $this->_history[$i];
    }

    /**
     * Go backward in the history.
     */
    public function previousHistory()
    {
        if (0 >= $this->_historyCurrent) {
            return $this->getHistory(0);
        }

        return $this->getHistory($this->_historyCurrent--);
    }

    /**
     * Go forward in the history.
     */
    public function nextHistory()
    {
        if ($this->_historyCurrent + 1 >= $this->_historySize) {
            return $this->getLine();
        }

        return $this->getHistory(++$this->_historyCurrent);
    }

    /**
     * Get current line.
     */
    public function getLine()
    {
        return $this->_line;
    }

    /**
     * Append to current line.
     */
    public function appendLine(string $append)
    {
        $this->_line .= $append;
        $this->_lineLength = \mb_strlen($this->_line);
        $this->_lineCurrent = $this->_lineLength;
    }

    /**
     * Insert into current line at the current seek.
     */
    public function insertLine(string $insert)
    {
        if ($this->_lineLength === $this->_lineCurrent) {
            return $this->appendLine($insert);
        }

        $this->_line = \mb_substr($this->_line, 0, $this->_lineCurrent).
                               $insert.
                               \mb_substr($this->_line, $this->_lineCurrent);
        $this->_lineLength = \mb_strlen($this->_line);
        $this->_lineCurrent += \mb_strlen($insert);

        return;
    }

    /**
     * Reset current line.
     */
    protected function resetLine()
    {
        $this->_line = null;
        $this->_lineCurrent = 0;
        $this->_lineLength = 0;
    }

    /**
     * Get current line seek.
     */
    public function getLineCurrent(): int
    {
        return $this->_lineCurrent;
    }

    /**
     * Get current line length.
     *
     * @return int
     */
    public function getLineLength(): int
    {
        return $this->_lineLength;
    }

    /**
     * Set prefix.
     */
    public function setPrefix(string $prefix)
    {
        $this->_prefix = $prefix;
    }

    /**
     * Get prefix.
     */
    public function getPrefix()
    {
        return $this->_prefix;
    }

    /**
     * Get buffer. Not for user.
     */
    public function getBuffer()
    {
        return $this->_buffer;
    }

    /**
     * Set an autocompleter.
     */
    public function setAutocompleter(Autocompleter $autocompleter)
    {
        $old = $this->_autocompleter;
        $this->_autocompleter = $autocompleter;

        return $old;
    }

    /**
     * Get the autocompleter.
     *
     * @return ?Autocompleter
     */
    public function getAutocompleter()
    {
        return $this->_autocompleter;
    }

    /**
     * Read on input. Not for user.
     */
    public function _read(int $length = 512): string
    {
        return Console::getInput()->read($length);
    }

    /**
     * Set current line. Not for user.
     */
    public function setLine(string $line)
    {
        $this->_line = $line;
        $this->_lineLength = \mb_strlen($this->_line ?: '');
        $this->_lineCurrent = $this->_lineLength;
    }

    /**
     * Set current line seek. Not for user.
     */
    public function setLineCurrent(int $current)
    {
        $this->_lineCurrent = $current;
    }

    /**
     * Set line length. Not for user.
     */
    public function setLineLength(int $length)
    {
        $this->_lineLength = $length;
    }

    /**
     * Set buffer. Not for user.
     */
    public function setBuffer(string $buffer)
    {
        $this->_buffer = $buffer;
    }

    /**
     * Up arrow binding.
     * Go backward in the history.
     */
    public function _bindArrowUp(self $self): int
    {
        if (0 === (static::STATE_CONTINUE & static::STATE_NO_ECHO)) {
            ConsoleCursor::clear('↔');
            Console::getOutput()->writeAll($self->getPrefix());
        }
        $buffer = $self->previousHistory() ?? '';
        $self->setBuffer($buffer);
        $self->setLine($buffer);

        return static::STATE_CONTINUE;
    }

    /**
     * Down arrow binding.
     * Go forward in the history.
     */
    public function _bindArrowDown(self $self): int
    {
        if (0 === (static::STATE_CONTINUE & static::STATE_NO_ECHO)) {
            ConsoleCursor::clear('↔');
            Console::getOutput()->writeAll($self->getPrefix());
        }

        $self->setBuffer($buffer = $self->nextHistory());
        $self->setLine($buffer);

        return static::STATE_CONTINUE;
    }

    /**
     * Right arrow binding.
     * Move cursor to the right.
     */
    public function _bindArrowRight(self $self): int
    {
        if ($self->getLineLength() > $self->getLineCurrent()) {
            if (0 === (static::STATE_CONTINUE & static::STATE_NO_ECHO)) {
                ConsoleCursor::move('→');
            }

            $self->setLineCurrent($self->getLineCurrent() + 1);
        }

        $self->setBuffer('');

        return static::STATE_CONTINUE;
    }

    /**
     * Left arrow binding.
     * Move cursor to the left.
     */
    public function _bindArrowLeft(self $self): int
    {
        if (0 < $self->getLineCurrent()) {
            if (0 === (static::STATE_CONTINUE & static::STATE_NO_ECHO)) {
                ConsoleCursor::move('←');
            }

            $self->setLineCurrent($self->getLineCurrent() - 1);
        }

        $self->setBuffer('');

        return static::STATE_CONTINUE;
    }

    /**
     * Backspace and Control-H binding.
     * Delete the first character at the right of the cursor.
     */
    public function _bindBackspace(self $self): int
    {
        $buffer = '';

        if (0 < $self->getLineCurrent()) {
            if (0 === (static::STATE_CONTINUE & static::STATE_NO_ECHO)) {
                ConsoleCursor::move('←');
                ConsoleCursor::clear('→');
            }

            if ($self->getLineLength() === $current = $self->getLineCurrent()) {
                $self->setLine(\mb_substr($self->getLine(), 0, -1));
            } else {
                $line = $self->getLine();
                $current = $self->getLineCurrent();
                $tail = \mb_substr($line, $current);
                $buffer = $tail.\str_repeat("\033[D", \mb_strlen($tail));
                $self->setLine(\mb_substr($line, 0, $current - 1).$tail);
                $self->setLineCurrent($current - 1);
            }
        }

        $self->setBuffer($buffer);

        return static::STATE_CONTINUE;
    }

    /**
     * Control-A binding.
     * Move cursor to beginning of line.
     */
    public function _bindControlA(self $self): int
    {
        for ($i = $self->getLineCurrent() - 1; 0 <= $i; --$i) {
            $self->_bindArrowLeft($self);
        }

        return static::STATE_CONTINUE;
    }

    /**
     * Control-B binding.
     * Move cursor backward one word.
     */
    public function _bindControlB(self $self): int
    {
        $current = $self->getLineCurrent();

        if (0 === $current) {
            return static::STATE_CONTINUE;
        }

        $words = \preg_split(
            '#\b#u',
            $self->getLine(),
            -1,
            \PREG_SPLIT_OFFSET_CAPTURE | \PREG_SPLIT_NO_EMPTY
        );

        for (
            $i = 0, $max = \count($words) - 1;
            $i < $max && $words[$i + 1][1] < $current;
            ++$i
        ) {
        }

        for ($j = $words[$i][1] + 1; $current >= $j; ++$j) {
            $self->_bindArrowLeft($self);
        }

        return static::STATE_CONTINUE;
    }

    /**
     * Control-E binding.
     * Move cursor to end of line.
     */
    public function _bindControlE(self $self): int
    {
        for (
            $i = $self->getLineCurrent(), $max = $self->getLineLength();
            $i < $max;
            ++$i
        ) {
            $self->_bindArrowRight($self);
        }

        return static::STATE_CONTINUE;
    }

    /**
     * Control-F binding.
     * Move cursor forward one word.
     */
    public function _bindControlF(self $self): int
    {
        $current = $self->getLineCurrent();

        if ($self->getLineLength() === $current) {
            return static::STATE_CONTINUE;
        }

        $words = \preg_split(
            '#\b#u',
            $self->getLine(),
            -1,
            \PREG_SPLIT_OFFSET_CAPTURE | \PREG_SPLIT_NO_EMPTY
        );

        for (
            $i = 0, $max = \count($words) - 1;
            $i < $max && $words[$i][1] < $current;
            ++$i
        ) {
        }

        if (!isset($words[$i + 1])) {
            $words[$i + 1] = [1 => $self->getLineLength()];
        }

        for ($j = $words[$i + 1][1]; $j > $current; --$j) {
            $self->_bindArrowRight($self);
        }

        return static::STATE_CONTINUE;
    }

    /**
     * Control-W binding.
     * Delete first backward word.
     */
    public function _bindControlW(self $self): int
    {
        $current = $self->getLineCurrent();

        if (0 === $current) {
            return static::STATE_CONTINUE;
        }

        $words = \preg_split(
            '#\b#u',
            $self->getLine(),
            -1,
            \PREG_SPLIT_OFFSET_CAPTURE | \PREG_SPLIT_NO_EMPTY
        );

        for (
            $i = 0, $max = \count($words) - 1;
            $i < $max && $words[$i + 1][1] < $current;
            ++$i
        ) {
        }

        for ($j = $words[$i][1] + 1; $current >= $j; ++$j) {
            $self->_bindBackspace($self);
        }

        return static::STATE_CONTINUE;
    }

    /**
     * Newline binding.
     */
    public function _bindNewline(self $self): int
    {
        $self->addHistory($self->getLine());

        return static::STATE_BREAK;
    }

    /**
     * Tab binding.
     */
    public function _bindTab(self $self): int
    {
        $output = Console::getOutput();
        $autocompleter = $self->getAutocompleter();
        $state = static::STATE_CONTINUE | static::STATE_NO_ECHO;

        if (null === $autocompleter) {
            return $state;
        }

        $current = $self->getLineCurrent();
        $line = $self->getLine();

        if (0 === $current) {
            return $state;
        }

        $matches = \preg_match_all(
            '#'.$autocompleter->getWordDefinition().'$#u',
            \mb_substr($line, 0, $current),
            $words
        );

        if (0 === $matches) {
            return $state;
        }

        $word = $words[0][0];

        if ('' === \trim($word)) {
            return $state;
        }

        $solution = $autocompleter->complete($word);
        $length = \mb_strlen($word);

        if (null === $solution) {
            return $state;
        }

        if (\is_array($solution)) {
            $_solution = $solution;
            $count = \count($_solution) - 1;
            $cWidth = 0;
            $window = ConsoleWindow::getSize();
            $wWidth = $window['x'];
            $cursor = ConsoleCursor::getPosition();

            \array_walk($_solution, function (&$value) use (&$cWidth) {
                $handle = \mb_strlen($value);

                if ($handle > $cWidth) {
                    $cWidth = $handle;
                }

                return;
            });
            \array_walk($_solution, function (&$value) use (&$cWidth) {
                $handle = \mb_strlen($value);

                if ($handle >= $cWidth) {
                    return;
                }

                $value .= \str_repeat(' ', $cWidth - $handle);

                return;
            });

            $mColumns = (int) \floor($wWidth / ($cWidth + 2));
            $mLines = (int) \ceil(($count + 1) / $mColumns);
            --$mColumns;
            $i = 0;

            if (0 > $window['y'] - $cursor['y'] - $mLines) {
                ConsoleWindow::scroll('↑', $mLines);
                ConsoleCursor::move('↑', $mLines);
            }

            ConsoleCursor::save();
            ConsoleCursor::hide();
            ConsoleCursor::move('↓ LEFT');
            ConsoleCursor::clear('↓');

            foreach ($_solution as $j => $s) {
                $output->writeAll("\033[0m".$s."\033[0m");

                if ($i++ < $mColumns) {
                    $output->writeAll('  ');
                } else {
                    $i = 0;

                    if (isset($_solution[$j + 1])) {
                        $output->writeAll("\n");
                    }
                }
            }

            ConsoleCursor::restore();
            ConsoleCursor::show();

            ++$mColumns;
            $input = Console::getInput();
            $read = [$input->getStream()->getStream()];
            $write = $except = [];
            $mColumn = -1;
            $mLine = -1;
            $coord = -1;
            $unselect = function () use (
                &$mColumn,
                &$mLine,
                &$coord,
                &$_solution,
                &$cWidth,
                $output
            ) {
                ConsoleCursor::save();
                ConsoleCursor::hide();
                ConsoleCursor::move('↓ LEFT');
                ConsoleCursor::move('→', $mColumn * ($cWidth + 2));
                ConsoleCursor::move('↓', $mLine);
                $output->writeAll("\033[0m".$_solution[$coord]."\033[0m");
                ConsoleCursor::restore();
                ConsoleCursor::show();

                return;
            };
            $select = function () use (
                &$mColumn,
                &$mLine,
                &$coord,
                &$_solution,
                &$cWidth,
                $output
            ) {
                ConsoleCursor::save();
                ConsoleCursor::hide();
                ConsoleCursor::move('↓ LEFT');
                ConsoleCursor::move('→', $mColumn * ($cWidth + 2));
                ConsoleCursor::move('↓', $mLine);
                $output->writeAll("\033[7m".$_solution[$coord]."\033[0m");
                ConsoleCursor::restore();
                ConsoleCursor::show();

                return;
            };
            $init = function () use (
                &$mColumn,
                &$mLine,
                &$coord,
                &$select
            ) {
                $mColumn = 0;
                $mLine = 0;
                $coord = 0;
                $select();

                return;
            };

            while (true) {
                @\stream_select($read, $write, $except, 30, 0);

                if (empty($read)) {
                    $read = [$input->getStream()->getStream()];

                    continue;
                }

                switch ($char = $self->_read()) {
                    case "\033[A":
                        if (-1 === $mColumn && -1 === $mLine) {
                            $init();

                            break;
                        }

                        $unselect();
                        $coord = \max(0, $coord - $mColumns);
                        $mLine = (int) \floor($coord / $mColumns);
                        $mColumn = $coord % $mColumns;
                        $select();

                        break;

                    case "\033[B":
                        if (-1 === $mColumn && -1 === $mLine) {
                            $init();

                            break;
                        }

                        $unselect();
                        $coord = \min($count, $coord + $mColumns);
                        $mLine = (int) \floor($coord / $mColumns);
                        $mColumn = $coord % $mColumns;
                        $select();

                        break;

                    case "\t":
                    case "\033[C":
                        if (-1 === $mColumn && -1 === $mLine) {
                            $init();

                            break;
                        }

                        $unselect();
                        $coord = \min($count, $coord + 1);
                        $mLine = (int) \floor($coord / $mColumns);
                        $mColumn = $coord % $mColumns;
                        $select();

                        break;

                    case "\033[D":
                        if (-1 === $mColumn && -1 === $mLine) {
                            $init();

                            break;
                        }

                        $unselect();
                        $coord = \max(0, $coord - 1);
                        $mLine = (int) \floor($coord / $mColumns);
                        $mColumn = $coord % $mColumns;
                        $select();

                        break;

                    case "\n":
                        if (-1 !== $mColumn && -1 !== $mLine) {
                            $tail = \mb_substr($line, $current);
                            $current -= $length;
                            $self->setLine(
                                \mb_substr($line, 0, $current).
                                $solution[$coord].
                                $tail
                            );
                            $self->setLineCurrent(
                                $current + \mb_strlen($solution[$coord])
                            );

                            ConsoleCursor::move('←', $length);
                            $output->writeAll($solution[$coord]);
                            ConsoleCursor::clear('→');
                            $output->writeAll($tail);
                            ConsoleCursor::move('←', \mb_strlen($tail));
                        }

                        // no break
                    default:
                        $mColumn = -1;
                        $mLine = -1;
                        $coord = -1;
                        ConsoleCursor::save();
                        ConsoleCursor::move('↓ LEFT');
                        ConsoleCursor::clear('↓');
                        ConsoleCursor::restore();

                        if ("\033" !== $char && "\n" !== $char) {
                            $self->setBuffer($char);

                            return $self->_readLine($char);
                        }

                        break 2;
                }
            }

            return $state;
        }

        $tail = \mb_substr($line, $current);
        $current -= $length;
        $self->setLine(
            \mb_substr($line, 0, $current).
            $solution.
            $tail
        );
        $self->setLineCurrent(
            $current + \mb_strlen($solution)
        );

        ConsoleCursor::move('←', $length);
        $output->writeAll($solution);
        ConsoleCursor::clear('→');
        $output->writeAll($tail);
        ConsoleCursor::move('←', \mb_strlen($tail));

        return $state;
    }
}

/*
 * Advanced interaction.
 */
Console::advancedInteraction();
