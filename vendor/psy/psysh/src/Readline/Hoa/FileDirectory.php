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
 * Class \Hoa\File\Directory.
 *
 * Directory handler.
 */
class FileDirectory extends FileGeneric
{
    /**
     * Open for reading.
     */
    const MODE_READ = 'rb';

    /**
     * Open for reading and writing. If the directory does not exist, attempt to
     * create it.
     */
    const MODE_CREATE = 'xb';

    /**
     * Open for reading and writing. If the directory does not exist, attempt to
     * create it recursively.
     */
    const MODE_CREATE_RECURSIVE = 'xrb';

    /**
     * Open a directory.
     */
    public function __construct(
        string $streamName,
        string $mode = self::MODE_READ,
        ?string $context = null,
        bool $wait = false
    ) {
        $this->setMode($mode);
        parent::__construct($streamName, $context, $wait);

        return;
    }

    /**
     * Open the stream and return the associated resource.
     */
    protected function &_open(string $streamName, ?StreamContext $context = null)
    {
        if (false === \is_dir($streamName)) {
            if ($this->getMode() === self::MODE_READ) {
                throw new FileDoesNotExistException('Directory %s does not exist.', 0, $streamName);
            } else {
                self::create(
                    $streamName,
                    $this->getMode(),
                    null !== $context
                        ? $context->getContext()
                        : null
                );
            }
        }

        $out = null;

        return $out;
    }

    /**
     * Close the current stream.
     */
    protected function _close(): bool
    {
        return true;
    }

    /**
     * Recursive copy of a directory.
     */
    public function copy(string $to, bool $force = StreamTouchable::DO_NOT_OVERWRITE): bool
    {
        if (empty($to)) {
            throw new FileException('The destination path (to copy) is empty.', 1);
        }

        $from = $this->getStreamName();
        $fromLength = \strlen($from) + 1;
        $finder = new FileFinder();
        $finder->in($from);

        self::create($to, self::MODE_CREATE_RECURSIVE);

        foreach ($finder as $file) {
            $relative = \substr($file->getPathname(), $fromLength);
            $_to = $to.\DIRECTORY_SEPARATOR.$relative;

            if (true === $file->isDir()) {
                self::create($_to, self::MODE_CREATE);

                continue;
            }

            // This is not possible to do `$file->open()->copy();
            // $file->close();` because the file will be opened in read and
            // write mode. In a PHAR for instance, this operation is
            // forbidden. So a special care must be taken to open file in read
            // only mode.
            $handle = null;

            if (true === $file->isFile()) {
                $handle = new FileRead($file->getPathname());
            } elseif (true === $file->isDir()) {
                $handle = new self($file->getPathName());
            } elseif (true === $file->isLink()) {
                $handle = new FileLinkRead($file->getPathName());
            }

            if (null !== $handle) {
                $handle->copy($_to, $force);
                $handle->close();
            }
        }

        return true;
    }

    /**
     * Delete a directory.
     */
    public function delete(): bool
    {
        $from = $this->getStreamName();
        $finder = new FileFinder();
        $finder->in($from)
               ->childFirst();

        foreach ($finder as $file) {
            $file->open()->delete();
            $file->close();
        }

        if (null === $this->getStreamContext()) {
            return @\rmdir($from);
        }

        return @\rmdir($from, $this->getStreamContext()->getContext());
    }

    /**
     * Create a directory.
     */
    public static function create(
        string $name,
        string $mode = self::MODE_CREATE_RECURSIVE,
        ?string $context = null
    ): bool {
        if (true === \is_dir($name)) {
            return true;
        }

        if (empty($name)) {
            return false;
        }

        if (null !== $context) {
            if (false === StreamContext::contextExists($context)) {
                throw new FileException('Context %s was not previously declared, cannot retrieve '.'this context.', 2, $context);
            } else {
                $context = StreamContext::getInstance($context);
            }
        }

        if (null === $context) {
            return @\mkdir(
                $name,
                0755,
                self::MODE_CREATE_RECURSIVE === $mode
            );
        }

        return @\mkdir(
            $name,
            0755,
            self::MODE_CREATE_RECURSIVE === $mode,
            $context->getContext()
        );
    }
}
