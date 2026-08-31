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
 * Class \Hoa\File\Link\ReadWrite.
 *
 * File handler.
 */
class FileLinkReadWrite extends FileLink implements StreamIn, StreamOut
{
    /**
     * Open a file.
     */
    public function __construct(
        string $streamName,
        string $mode = parent::MODE_APPEND_READ_WRITE,
        ?string $context = null,
        bool $wait = false
    ) {
        parent::__construct($streamName, $mode, $context, $wait);

        return;
    }

    /**
     * Open the stream and return the associated resource.
     */
    protected function &_open(string $streamName, ?StreamContext $context = null)
    {
        static $createModes = [
            parent::MODE_READ_WRITE,
            parent::MODE_TRUNCATE_READ_WRITE,
            parent::MODE_APPEND_READ_WRITE,
            parent::MODE_CREATE_READ_WRITE,
        ];

        if (!\in_array($this->getMode(), $createModes)) {
            throw new FileException('Open mode are not supported; given %d. Only %s are supported.', 0, [$this->getMode(), \implode(', ', $createModes)]);
        }

        \preg_match('#^(\w+)://#', $streamName, $match);

        if (((isset($match[1]) && $match[1] === 'file') || !isset($match[1])) &&
            !\file_exists($streamName) &&
            parent::MODE_READ_WRITE === $this->getMode()) {
            throw new FileDoesNotExistException('File %s does not exist.', 1, $streamName);
        }

        $out = parent::_open($streamName, $context);

        return $out;
    }

    /**
     * Test for end-of-file.
     */
    public function eof(): bool
    {
        return \feof($this->getStream());
    }

    /**
     * Read n characters.
     */
    public function read(int $length)
    {
        if (0 > $length) {
            throw new FileException('Length must be greater than 0, given %d.', 2, $length);
        }

        return \fread($this->getStream(), $length);
    }

    /**
     * Alias of $this->read().
     */
    public function readString(int $length)
    {
        return $this->read($length);
    }

    /**
     * Read a character.
     */
    public function readCharacter()
    {
        return \fgetc($this->getStream());
    }

    /**
     * Read a boolean.
     */
    public function readBoolean()
    {
        return (bool) $this->read(1);
    }

    /**
     * Read an integer.
     */
    public function readInteger(int $length = 1)
    {
        return (int) $this->read($length);
    }

    /**
     * Read a float.
     */
    public function readFloat(int $length = 1)
    {
        return (float) $this->read($length);
    }

    /**
     * Read an array.
     * Alias of the $this->scanf() method.
     */
    public function readArray(?string $format = null)
    {
        return $this->scanf($format);
    }

    /**
     * Read a line.
     */
    public function readLine()
    {
        return \fgets($this->getStream());
    }

    /**
     * Read all, i.e. read as much as possible.
     */
    public function readAll(int $offset = 0)
    {
        return \stream_get_contents($this->getStream(), -1, $offset);
    }

    /**
     * Parse input from a stream according to a format.
     */
    public function scanf(string $format): array
    {
        return \fscanf($this->getStream(), $format);
    }

    /**
     * Write n characters.
     */
    public function write(string $string, int $length)
    {
        if (0 > $length) {
            throw new FileException('Length must be greater than 0, given %d.', 3, $length);
        }

        return \fwrite($this->getStream(), $string, $length);
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
    public function writeCharacter(string $char)
    {
        return $this->write((string) $char[0], 1);
    }

    /**
     * Write a boolean.
     */
    public function writeBoolean(bool $boolean)
    {
        return $this->write((string) (bool) $boolean, 1);
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
        return $this->write($string, \strlen($string));
    }

    /**
     * Truncate a file to a given length.
     */
    public function truncate(int $size): bool
    {
        return \ftruncate($this->getStream(), $size);
    }
}
