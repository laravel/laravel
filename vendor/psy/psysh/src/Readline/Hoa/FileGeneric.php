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
 * Class \Hoa\File\Generic.
 *
 * Describe a super-file.
 */
abstract class FileGeneric extends Stream implements StreamPathable, StreamStatable, StreamTouchable
{
    /**
     * Mode.
     */
    protected $_mode = null;

    /**
     * Get filename component of path.
     */
    public function getBasename(): string
    {
        return \basename($this->getStreamName());
    }

    /**
     * Get directory name component of path.
     */
    public function getDirname(): string
    {
        return \dirname($this->getStreamName());
    }

    /**
     * Get size.
     */
    public function getSize(): int
    {
        if (false === $this->getStatistic()) {
            return false;
        }

        return \filesize($this->getStreamName());
    }

    /**
     * Get informations about a file.
     */
    public function getStatistic(): array
    {
        return \fstat($this->getStream());
    }

    /**
     * Get last access time of file.
     */
    public function getATime(): int
    {
        return \fileatime($this->getStreamName());
    }

    /**
     * Get inode change time of file.
     */
    public function getCTime(): int
    {
        return \filectime($this->getStreamName());
    }

    /**
     * Get file modification time.
     */
    public function getMTime(): int
    {
        return \filemtime($this->getStreamName());
    }

    /**
     * Get file group.
     */
    public function getGroup(): int
    {
        return \filegroup($this->getStreamName());
    }

    /**
     * Get file owner.
     */
    public function getOwner(): int
    {
        return \fileowner($this->getStreamName());
    }

    /**
     * Get file permissions.
     */
    public function getPermissions(): int
    {
        return \fileperms($this->getStreamName());
    }

    /**
     * Get file permissions as a string.
     * Result sould be interpreted like this:
     *     * s: socket;
     *     * l: symbolic link;
     *     * -: regular;
     *     * b: block special;
     *     * d: directory;
     *     * c: character special;
     *     * p: FIFO pipe;
     *     * u: unknown.
     */
    public function getReadablePermissions(): string
    {
        $p = $this->getPermissions();

        if (($p & 0xC000) === 0xC000) {
            $out = 's';
        } elseif (($p & 0xA000) === 0xA000) {
            $out = 'l';
        } elseif (($p & 0x8000) === 0x8000) {
            $out = '-';
        } elseif (($p & 0x6000) === 0x6000) {
            $out = 'b';
        } elseif (($p & 0x4000) === 0x4000) {
            $out = 'd';
        } elseif (($p & 0x2000) === 0x2000) {
            $out = 'c';
        } elseif (($p & 0x1000) === 0x1000) {
            $out = 'p';
        } else {
            $out = 'u';
        }

        $out .=
            (($p & 0x0100) ? 'r' : '-').
            (($p & 0x0080) ? 'w' : '-').
            (($p & 0x0040) ?
            (($p & 0x0800) ? 's' : 'x') :
            (($p & 0x0800) ? 'S' : '-')).
            (($p & 0x0020) ? 'r' : '-').
            (($p & 0x0010) ? 'w' : '-').
            (($p & 0x0008) ?
            (($p & 0x0400) ? 's' : 'x') :
            (($p & 0x0400) ? 'S' : '-')).
            (($p & 0x0004) ? 'r' : '-').
            (($p & 0x0002) ? 'w' : '-').
            (($p & 0x0001) ?
            (($p & 0x0200) ? 't' : 'x') :
            (($p & 0x0200) ? 'T' : '-'));

        return $out;
    }

    /**
     * Check if the file is readable.
     */
    public function isReadable(): bool
    {
        return \is_readable($this->getStreamName());
    }

    /**
     * Check if the file is writable.
     */
    public function isWritable(): bool
    {
        return \is_writable($this->getStreamName());
    }

    /**
     * Check if the file is executable.
     */
    public function isExecutable(): bool
    {
        return \is_executable($this->getStreamName());
    }

    /**
     * Clear file status cache.
     */
    public function clearStatisticCache()
    {
        \clearstatcache(true, $this->getStreamName());
    }

    /**
     * Clear all files status cache.
     */
    public static function clearAllStatisticCaches()
    {
        \clearstatcache();
    }

    /**
     * Set access and modification time of file.
     */
    public function touch(?int $time = null, ?int $atime = null): bool
    {
        if (null === $time) {
            $time = \time();
        }

        if (null === $atime) {
            $atime = $time;
        }

        return \touch($this->getStreamName(), $time, $atime);
    }

    /**
     * Copy file.
     * Return the destination file path if succeed, false otherwise.
     */
    public function copy(string $to, bool $force = StreamTouchable::DO_NOT_OVERWRITE): bool
    {
        $from = $this->getStreamName();

        if ($force === StreamTouchable::DO_NOT_OVERWRITE &&
            true === \file_exists($to)) {
            return true;
        }

        if (null === $this->getStreamContext()) {
            return @\copy($from, $to);
        }

        return @\copy($from, $to, $this->getStreamContext()->getContext());
    }

    /**
     * Move a file.
     */
    public function move(
        string $name,
        bool $force = StreamTouchable::DO_NOT_OVERWRITE,
        bool $mkdir = StreamTouchable::DO_NOT_MAKE_DIRECTORY
    ): bool {
        $from = $this->getStreamName();

        if ($force === StreamTouchable::DO_NOT_OVERWRITE &&
            true === \file_exists($name)) {
            return false;
        }

        if (StreamTouchable::MAKE_DIRECTORY === $mkdir) {
            FileDirectory::create(
                \dirname($name),
                FileDirectory::MODE_CREATE_RECURSIVE
            );
        }

        if (null === $this->getStreamContext()) {
            return @\rename($from, $name);
        }

        return @\rename($from, $name, $this->getStreamContext()->getContext());
    }

    /**
     * Delete a file.
     */
    public function delete(): bool
    {
        if (null === $this->getStreamContext()) {
            return @\unlink($this->getStreamName());
        }

        return @\unlink(
            $this->getStreamName(),
            $this->getStreamContext()->getContext()
        );
    }

    /**
     * Change file group.
     */
    public function changeGroup($group): bool
    {
        return \chgrp($this->getStreamName(), $group);
    }

    /**
     * Change file mode.
     */
    public function changeMode(int $mode): bool
    {
        return \chmod($this->getStreamName(), $mode);
    }

    /**
     * Change file owner.
     */
    public function changeOwner($user): bool
    {
        return \chown($this->getStreamName(), $user);
    }

    /**
     * Change the current umask.
     */
    public static function umask(?int $umask = null): int
    {
        if (null === $umask) {
            return \umask();
        }

        return \umask($umask);
    }

    /**
     * Check if it is a file.
     */
    public function isFile(): bool
    {
        return \is_file($this->getStreamName());
    }

    /**
     * Check if it is a link.
     */
    public function isLink(): bool
    {
        return \is_link($this->getStreamName());
    }

    /**
     * Check if it is a directory.
     */
    public function isDirectory(): bool
    {
        return \is_dir($this->getStreamName());
    }

    /**
     * Check if it is a socket.
     */
    public function isSocket(): bool
    {
        return \filetype($this->getStreamName()) === 'socket';
    }

    /**
     * Check if it is a FIFO pipe.
     */
    public function isFIFOPipe(): bool
    {
        return \filetype($this->getStreamName()) === 'fifo';
    }

    /**
     * Check if it is character special file.
     */
    public function isCharacterSpecial(): bool
    {
        return \filetype($this->getStreamName()) === 'char';
    }

    /**
     * Check if it is block special.
     */
    public function isBlockSpecial(): bool
    {
        return \filetype($this->getStreamName()) === 'block';
    }

    /**
     * Check if it is an unknown type.
     */
    public function isUnknown(): bool
    {
        return \filetype($this->getStreamName()) === 'unknown';
    }

    /**
     * Set the open mode.
     */
    protected function setMode(string $mode)
    {
        $old = $this->_mode;
        $this->_mode = $mode;

        return $old;
    }

    /**
     * Get the open mode.
     */
    public function getMode()
    {
        return $this->_mode;
    }

    /**
     * Get inode.
     */
    public function getINode(): int
    {
        return \fileinode($this->getStreamName());
    }

    /**
     * Check if the system is case sensitive or not.
     */
    public static function isCaseSensitive(): bool
    {
        return !(
            \file_exists(\mb_strtolower(__FILE__)) &&
            \file_exists(\mb_strtoupper(__FILE__))
        );
    }

    /**
     * Get a canonicalized absolute pathname.
     */
    public function getRealPath(): string
    {
        if (false === $out = \realpath($this->getStreamName())) {
            return $this->getStreamName();
        }

        return $out;
    }

    /**
     * Get file extension (if exists).
     */
    public function getExtension(): string
    {
        return \pathinfo(
            $this->getStreamName(),
            \PATHINFO_EXTENSION
        );
    }

    /**
     * Get filename without extension.
     */
    public function getFilename(): string
    {
        $file = \basename($this->getStreamName());

        if (\defined('PATHINFO_FILENAME')) {
            return \pathinfo($file, \PATHINFO_FILENAME);
        }

        if (\strstr($file, '.')) {
            return \substr($file, 0, \strrpos($file, '.'));
        }

        return $file;
    }
}
