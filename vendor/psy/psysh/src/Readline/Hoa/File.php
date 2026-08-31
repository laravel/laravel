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
 * Class \Hoa\File.
 *
 * File handler.
 */
abstract class File extends FileGeneric implements StreamBufferable, StreamLockable, StreamPointable
{
    /**
     * Open for reading only; place the file pointer at the beginning of the
     * file.
     */
    const MODE_READ = 'rb';

    /**
     * Open for reading and writing; place the file pointer at the beginning of
     * the file.
     */
    const MODE_READ_WRITE = 'r+b';

    /**
     * Open for writing only; place the file pointer at the beginning of the
     * file and truncate the file to zero length. If the file does not exist,
     * attempt to create it.
     */
    const MODE_TRUNCATE_WRITE = 'wb';

    /**
     * Open for reading and writing; place the file pointer at the beginning of
     * the file and truncate the file to zero length. If the file does not
     * exist, attempt to create it.
     */
    const MODE_TRUNCATE_READ_WRITE = 'w+b';

    /**
     * Open for writing only; place the file pointer at the end of the file. If
     * the file does not exist, attempt to create it.
     */
    const MODE_APPEND_WRITE = 'ab';

    /**
     * Open for reading and writing; place the file pointer at the end of the
     * file. If the file does not exist, attempt to create it.
     */
    const MODE_APPEND_READ_WRITE = 'a+b';

    /**
     * Create and open for writing only; place the file pointer at the beginning
     * of the file. If the file already exits, the fopen() call with fail by
     * returning false and generating an error of level E_WARNING. If the file
     * does not exist, attempt to create it. This is equivalent to specifying
     * O_EXCL | O_CREAT flags for the underlying open(2) system call.
     */
    const MODE_CREATE_WRITE = 'xb';

    /**
     * Create and open for reading and writing; place the file pointer at the
     * beginning of the file. If the file already exists, the fopen() call with
     * fail by returning false and generating an error of level E_WARNING. If
     * the file does not exist, attempt to create it. This is equivalent to
     * specifying O_EXCL | O_CREAT flags for the underlying open(2) system call.
     */
    const MODE_CREATE_READ_WRITE = 'x+b';

    /**
     * Open a file.
     */
    public function __construct(
        string $streamName,
        string $mode,
        ?string $context = null,
        bool $wait = false
    ) {
        $this->setMode($mode);

        switch ($streamName) {
            case '0':
                $streamName = 'php://stdin';

                break;

            case '1':
                $streamName = 'php://stdout';

                break;

            case '2':
                $streamName = 'php://stderr';

                break;

            default:
                if (true === \ctype_digit($streamName)) {
                    $streamName = 'php://fd/'.$streamName;
                }
        }

        parent::__construct($streamName, $context, $wait);

        return;
    }

    /**
     * Open the stream and return the associated resource.
     */
    protected function &_open(string $streamName, ?StreamContext $context = null)
    {
        if (\substr($streamName, 0, 4) === 'file' &&
            false === \is_dir(\dirname($streamName))) {
            throw new FileException('Directory %s does not exist. Could not open file %s.', 1, [\dirname($streamName), \basename($streamName)]);
        }

        if (null === $context) {
            if (false === $out = @\fopen($streamName, $this->getMode(), true)) {
                throw new FileException('Failed to open stream %s.', 2, $streamName);
            }

            return $out;
        }

        $out = @\fopen(
            $streamName,
            $this->getMode(),
            true,
            $context->getContext()
        );

        if (false === $out) {
            throw new FileException('Failed to open stream %s.', 3, $streamName);
        }

        return $out;
    }

    /**
     * Close the current stream.
     */
    protected function _close(): bool
    {
        return @\fclose($this->getStream());
    }

    /**
     * Start a new buffer.
     * The callable acts like a light filter.
     */
    public function newBuffer($callable = null, ?int $size = null): int
    {
        $this->setStreamBuffer($size);

        // @todo manage $callable as a filter?

        return 1;
    }

    /**
     * Flush the output to a stream.
     */
    public function flush(): bool
    {
        return \fflush($this->getStream());
    }

    /**
     * Delete buffer.
     */
    public function deleteBuffer(): bool
    {
        return $this->disableStreamBuffer();
    }

    /**
     * Get bufffer level.
     */
    public function getBufferLevel(): int
    {
        return 1;
    }

    /**
     * Get buffer size.
     */
    public function getBufferSize(): int
    {
        return $this->getStreamBufferSize();
    }

    /**
     * Portable advisory locking.
     */
    public function lock(int $operation): bool
    {
        return \flock($this->getStream(), $operation);
    }

    /**
     * Rewind the position of a stream pointer.
     */
    public function rewind(): bool
    {
        return \rewind($this->getStream());
    }

    /**
     * Seek on a stream pointer.
     */
    public function seek(int $offset, int $whence = StreamPointable::SEEK_SET): int
    {
        return \fseek($this->getStream(), $offset, $whence);
    }

    /**
     * Get the current position of the stream pointer.
     */
    public function tell(): int
    {
        $stream = $this->getStream();

        if (null === $stream) {
            return 0;
        }

        return \ftell($stream);
    }

    /**
     * Create a file.
     */
    public static function create(string $name)
    {
        if (\file_exists($name)) {
            return true;
        }

        return \touch($name);
    }
}
