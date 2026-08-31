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
 * Class \Hoa\Console\Output.
 *
 * This class represents the output of a program. Most of the time, this is
 * going to be STDOUT.
 */
class ConsoleOutput implements StreamOut
{
    /**
     * Whether the multiplexer must be considered while writing on the output.
     */
    protected $_considerMultiplexer = false;

    /**
     * Real output stream.
     */
    protected $_output = null;

    /**
     * Wraps an `Hoa\Stream\IStream\Out` stream.
     */
    public function __construct(?StreamOut $output = null)
    {
        $this->_output = $output;

        return;
    }

    /**
     * Get the real output stream.
     */
    public function getStream(): StreamOut
    {
        return $this->_output;
    }

    /**
     * Write n characters.
     */
    public function write(string $string, int $length)
    {
        if (0 > $length) {
            throw new ConsoleException('Length must be greater than 0, given %d.', 0, $length);
        }

        $out = \substr($string, 0, $length);

        if (true === $this->isMultiplexerConsidered()) {
            if (true === Console::isTmuxRunning()) {
                $out =
                    "\033Ptmux;".
                    \str_replace("\033", "\033\033", $out).
                    "\033\\";
            }

            $length = \strlen($out);
        }

        if (null === $this->_output) {
            echo $out;
        } else {
            $this->_output->write($out, $length);
        }
    }

    /**
     * Write a string.
     */
    public function writeString(string $string)
    {
        $string = (string) $string;

        return $this->write($string, \strlen($string));
    }

    /**
     * Write a character.
     */
    public function writeCharacter(string $character)
    {
        return $this->write((string) $character[0], 1);
    }

    /**
     * Write a boolean.
     */
    public function writeBoolean(bool $boolean)
    {
        return $this->write(((bool) $boolean) ? '1' : '0', 1);
    }

    /**
     * Write an integer.
     */
    public function writeInteger(int $integer)
    {
        $integer = (string) (int) $integer;

        return $this->write($integer, \strlen($integer));
    }

    /**
     * Write a float.
     */
    public function writeFloat(float $float)
    {
        $float = (string) (float) $float;

        return $this->write($float, \strlen($float));
    }

    /**
     * Write an array.
     */
    public function writeArray(array $array)
    {
        $array = \var_export($array, true);

        return $this->write($array, \strlen($array));
    }

    /**
     * Write a line.
     */
    public function writeLine(string $line)
    {
        if (false === $n = \strpos($line, "\n")) {
            return $this->write($line."\n", \strlen($line) + 1);
        }

        ++$n;

        return $this->write(\substr($line, 0, $n), $n);
    }

    /**
     * Write all, i.e. as much as possible.
     */
    public function writeAll(string $string)
    {
        return $this->write($string ?? '', \strlen($string ?? ''));
    }

    /**
     * Truncate a stream to a given length.
     */
    public function truncate(int $size): bool
    {
        return false;
    }

    /**
     * Consider the multiplexer (if running) while writing on the output.
     */
    public function considerMultiplexer(bool $consider): bool
    {
        $old = $this->_considerMultiplexer;
        $this->_considerMultiplexer = $consider;

        return $old;
    }

    /**
     * Check whether the multiplexer must be considered or not.
     */
    public function isMultiplexerConsidered(): bool
    {
        return $this->_considerMultiplexer;
    }
}
