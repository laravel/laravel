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
 * Stream wrapper for the `hoa://` protocol.
 */
class ProtocolWrapper
{
    /**
     * Opened stream as a resource.
     */
    private $_stream = null;

    /**
     * Stream name (filename).
     */
    private $_streamName = null;

    /**
     * Stream context (given by the streamWrapper class) as a resource.
     */
    public $context = null;

    /**
     * Get the real path of the given URL.
     * Could return false if the path cannot be reached.
     */
    public static function realPath(string $path, bool $exists = true)
    {
        return ProtocolNode::getRoot()->resolve($path, $exists);
    }

    /**
     * Retrieve the underlying resource.
     *
     * `$castAs` can be `STREAM_CAST_FOR_SELECT` when `stream_select` is
     * calling `stream_cast` or `STREAM_CAST_AS_STREAM` when `stream_cast` is
     * called for other uses.
     */
    public function stream_cast(int $castAs)
    {
        return null;
    }

    /**
     * Closes a resource.
     * This method is called in response to `fclose`.
     * All resources that were locked, or allocated, by the wrapper should be
     * released.
     */
    public function stream_close()
    {
        if (true === @\fclose($this->getStream())) {
            $this->_stream = null;
            $this->_streamName = null;
        }
    }

    /**
     * Tests for end-of-file on a file pointer.
     * This method is called in response to feof().
     */
    public function stream_eof(): bool
    {
        return \feof($this->getStream());
    }

    /**
     * Flush the output.
     * This method is called in respond to fflush().
     * If we have cached data in our stream but not yet stored it into the
     * underlying storage, we should do so now.
     */
    public function stream_flush(): bool
    {
        return \fflush($this->getStream());
    }

    /**
     * Advisory file locking.
     * This method is called in response to flock(), when file_put_contents()
     * (when flags contains LOCK_EX), stream_set_blocking() and when closing the
     * stream (LOCK_UN).
     *
     * Operation is one the following:
     *   * LOCK_SH to acquire a shared lock (reader) ;
     *   * LOCK_EX to acquire an exclusive lock (writer) ;
     *   * LOCK_UN to release a lock (shared or exclusive) ;
     *   * LOCK_NB if we don't want flock() to
     *     block while locking (not supported on
     *     Windows).
     */
    public function stream_lock(int $operation): bool
    {
        return \flock($this->getStream(), $operation);
    }

    /**
     * Change stream options.
     * This method is called to set metadata on the stream. It is called when
     * one of the following functions is called on a stream URL: touch, chmod,
     * chown or chgrp.
     *
     * Option must be one of the following constant:
     *   * STREAM_META_TOUCH,
     *   * STREAM_META_OWNER_NAME,
     *   * STREAM_META_OWNER,
     *   * STREAM_META_GROUP_NAME,
     *   * STREAM_META_GROUP,
     *   * STREAM_META_ACCESS.
     *
     * Values are arguments of `touch`, `chmod`, `chown`, and `chgrp`.
     */
    public function stream_metadata(string $path, int $option, $values): bool
    {
        $path = static::realPath($path, false);

        switch ($option) {
            case \STREAM_META_TOUCH:
                $arity = \count($values);

                if (0 === $arity) {
                    $out = \touch($path);
                } elseif (1 === $arity) {
                    $out = \touch($path, $values[0]);
                } else {
                    $out = \touch($path, $values[0], $values[1]);
                }

                break;

            case \STREAM_META_OWNER_NAME:
            case \STREAM_META_OWNER:
                $out = \chown($path, $values);

                break;

            case \STREAM_META_GROUP_NAME:
            case \STREAM_META_GROUP:
                $out = \chgrp($path, $values);

                break;

            case \STREAM_META_ACCESS:
                $out = \chmod($path, $values);

                break;

            default:
                $out = false;
        }

        return $out;
    }

    /**
     * Open file or URL.
     * This method is called immediately after the wrapper is initialized (f.e.
     * by fopen() and file_get_contents()).
     */
    public function stream_open(string $path, string $mode, int $options, &$openedPath): bool
    {
        $path = static::realPath($path, 'r' === $mode[0]);

        if (Protocol::NO_RESOLUTION === $path) {
            return false;
        }

        if (null === $this->context) {
            $openedPath = \fopen($path, $mode, $options & \STREAM_USE_PATH);
        } else {
            $openedPath = \fopen(
                $path,
                $mode,
                (bool) ($options & \STREAM_USE_PATH),
                $this->context
            );
        }

        if (false === \is_resource($openedPath)) {
            return false;
        }

        $this->_stream = $openedPath;
        $this->_streamName = $path;

        return true;
    }

    /**
     * Read from stream.
     * This method is called in response to fread() and fgets().
     */
    public function stream_read(int $size): string
    {
        return \fread($this->getStream(), $size);
    }

    /**
     * Seek to specific location in a stream.
     * This method is called in response to fseek().
     * The read/write position of the stream should be updated according to the
     * $offset and $whence.
     *
     * The possible values for `$whence` are:
     *   * SEEK_SET to set position equal to $offset bytes,
     *   * SEEK_CUR to set position to current location plus `$offset`,
     *   * SEEK_END to set position to end-of-file plus `$offset`.
     */
    public function stream_seek(int $offset, int $whence = \SEEK_SET): bool
    {
        return 0 === \fseek($this->getStream(), $offset, $whence);
    }

    /**
     * Retrieve information about a file resource.
     * This method is called in response to fstat().
     */
    public function stream_stat(): array
    {
        return \fstat($this->getStream());
    }

    /**
     * Retrieve the current position of a stream.
     * This method is called in response to ftell().
     */
    public function stream_tell(): int
    {
        return \ftell($this->getStream());
    }

    /**
     * Truncate a stream to a given length.
     */
    public function stream_truncate(int $size): bool
    {
        return \ftruncate($this->getStream(), $size);
    }

    /**
     * Write to stream.
     * This method is called in response to fwrite().
     */
    public function stream_write(string $data): int
    {
        return \fwrite($this->getStream(), $data);
    }

    /**
     * Close directory handle.
     * This method is called in to closedir().
     * Any resources which were locked, or allocated, during opening and use of
     * the directory stream should be released.
     */
    public function dir_closedir()
    {
        \closedir($this->getStream());
        $this->_stream = null;
        $this->_streamName = null;
    }

    /**
     * Open directory handle.
     * This method is called in response to opendir().
     *
     * The `$options` input represents whether or not to enforce safe_mode
     * (0x04). It is not used here.
     */
    public function dir_opendir(string $path, int $options): bool
    {
        $path = static::realPath($path);
        $handle = null;

        if (null === $this->context) {
            $handle = @\opendir($path);
        } else {
            $handle = @\opendir($path, $this->context);
        }

        if (false === $handle) {
            return false;
        }

        $this->_stream = $handle;
        $this->_streamName = $path;

        return true;
    }

    /**
     * Read entry from directory handle.
     * This method is called in response to readdir().
     *
     * @return mixed
     */
    public function dir_readdir()
    {
        return \readdir($this->getStream());
    }

    /**
     * Rewind directory handle.
     * This method is called in response to rewinddir().
     * Should reset the output generated by self::dir_readdir, i.e. the next
     * call to self::dir_readdir should return the first entry in the location
     * returned by self::dir_opendir.
     */
    public function dir_rewinddir()
    {
        \rewinddir($this->getStream());
    }

    /**
     * Create a directory.
     * This method is called in response to mkdir().
     */
    public function mkdir(string $path, int $mode, int $options): bool
    {
        if (null === $this->context) {
            return \mkdir(
                static::realPath($path, false),
                $mode,
                $options | \STREAM_MKDIR_RECURSIVE
            );
        }

        return \mkdir(
            static::realPath($path, false),
            $mode,
            (bool) ($options | \STREAM_MKDIR_RECURSIVE),
            $this->context
        );
    }

    /**
     * Rename a file or directory.
     * This method is called in response to rename().
     * Should attempt to rename $from to $to.
     */
    public function rename(string $from, string $to): bool
    {
        if (null === $this->context) {
            return \rename(static::realPath($from), static::realPath($to, false));
        }

        return \rename(
            static::realPath($from),
            static::realPath($to, false),
            $this->context
        );
    }

    /**
     * Remove a directory.
     * This method is called in response to rmdir().
     * The `$options` input is a bitwise mask of values. It is not used here.
     */
    public function rmdir(string $path, int $options): bool
    {
        if (null === $this->context) {
            return \rmdir(static::realPath($path));
        }

        return \rmdir(static::realPath($path), $this->context);
    }

    /**
     * Delete a file.
     * This method is called in response to unlink().
     */
    public function unlink(string $path): bool
    {
        if (null === $this->context) {
            return \unlink(static::realPath($path));
        }

        return \unlink(static::realPath($path), $this->context);
    }

    /**
     * Retrieve information about a file.
     * This method is called in response to all stat() related functions.
     * The `$flags` input holds additional flags set by the streams API.  It
     * can hold one or more of the following values OR'd together.
     * STREAM_URL_STAT_LINK: for resource with the ability to link to other
     * resource (such as an HTTP location: forward, or a filesystem
     * symlink). This flag specified that only information about the link
     * itself should be returned, not the resource pointed to by the
     * link. This flag is set in response to calls to lstat(), is_link(), or
     * filetype().  STREAM_URL_STAT_QUIET: if this flag is set, our wrapper
     * should not raise any errors. If this flag is not set, we are
     * responsible for reporting errors using the trigger_error() function
     * during stating of the path.
     */
    public function url_stat(string $path, int $flags)
    {
        $path = static::realPath($path);

        if (Protocol::NO_RESOLUTION === $path) {
            if ($flags & \STREAM_URL_STAT_QUIET) {
                return 0;
            } else {
                return \trigger_error(
                    'Path '.$path.' cannot be resolved.',
                    \E_WARNING
                );
            }
        }

        if ($flags & \STREAM_URL_STAT_LINK) {
            return @\lstat($path);
        }

        return @\stat($path);
    }

    /**
     * Get stream resource.
     */
    public function getStream()
    {
        return $this->_stream;
    }

    /**
     * Get stream name.
     */
    public function getStreamName()
    {
        return $this->_streamName;
    }
}

/*
 * Register the `hoa://` protocol.
 */
\stream_wrapper_register('hoa', ProtocolWrapper::class);
