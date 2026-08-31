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
 * Class \Hoa\File\Link\Read.
 *
 * File handler.
 *
 * @license    New BSD License
 */
class FileLinkRead extends FileLink implements StreamIn
{
    /**
     * Open a file.
     *
     * @param string $streamName stream name
     * @param string $mode       open mode, see the parent::MODE_* constants
     * @param string $context    context ID (please, see the
     *                           \Hoa\Stream\Context class)
     * @param bool   $wait       differ opening or not
     */
    public function __construct(
        string $streamName,
        string $mode = parent::MODE_READ,
        ?string $context = null,
        bool $wait = false
    ) {
        parent::__construct($streamName, $mode, $context, $wait);

        return;
    }

    /**
     * Open the stream and return the associated resource.
     *
     * @param string              $streamName Stream name (e.g. path or URL).
     * @param \Hoa\Stream\Context $context    context
     *
     * @return resource
     *
     * @throws \Hoa\File\Exception\FileDoesNotExist
     * @throws \Hoa\File\Exception
     */
    protected function &_open(string $streamName, ?StreamContext $context = null)
    {
        static $createModes = [
            parent::MODE_READ,
        ];

        if (!\in_array($this->getMode(), $createModes)) {
            throw new FileException('Open mode are not supported; given %d. Only %s are supported.', 0, [$this->getMode(), \implode(', ', $createModes)]);
        }

        \preg_match('#^(\w+)://#', $streamName, $match);

        if (((isset($match[1]) && $match[1] === 'file') || !isset($match[1])) &&
            !\file_exists($streamName)) {
            throw new FileDoesNotExistException('File %s does not exist.', 1, $streamName);
        }

        $out = parent::_open($streamName, $context);

        return $out;
    }

    /**
     * Test for end-of-file.
     *
     * @return bool
     */
    public function eof(): bool
    {
        return \feof($this->getStream());
    }

    /**
     * Read n characters.
     *
     * @param int $length length
     *
     * @return string
     *
     * @throws \Hoa\File\Exception
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
     *
     * @param int $length length
     *
     * @return string
     */
    public function readString(int $length)
    {
        return $this->read($length);
    }

    /**
     * Read a character.
     *
     * @return string
     */
    public function readCharacter()
    {
        return \fgetc($this->getStream());
    }

    /**
     * Read a boolean.
     *
     * @return bool
     */
    public function readBoolean()
    {
        return (bool) $this->read(1);
    }

    /**
     * Read an integer.
     *
     * @param int $length length
     *
     * @return int
     */
    public function readInteger(int $length = 1)
    {
        return (int) $this->read($length);
    }

    /**
     * Read a float.
     *
     * @param int $length length
     *
     * @return float
     */
    public function readFloat(int $length = 1)
    {
        return (float) $this->read($length);
    }

    /**
     * Read an array.
     * Alias of the $this->scanf() method.
     *
     * @param string $format format (see printf's formats)
     *
     * @return array
     */
    public function readArray(?string $format = null)
    {
        return $this->scanf($format);
    }

    /**
     * Read a line.
     *
     * @return string
     */
    public function readLine()
    {
        return \fgets($this->getStream());
    }

    /**
     * Read all, i.e. read as much as possible.
     *
     * @param int $offset offset
     *
     * @return string
     */
    public function readAll(int $offset = 0)
    {
        return \stream_get_contents($this->getStream(), -1, $offset);
    }

    /**
     * Parse input from a stream according to a format.
     *
     * @param string $format format (see printf's formats)
     *
     * @return array
     */
    public function scanf(string $format): array
    {
        return \fscanf($this->getStream(), $format);
    }
}
