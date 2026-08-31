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
 * Interface \Hoa\Stream\IStream\Statable.
 *
 * Interface for statable input/output.
 */
interface StreamStatable extends IStream
{
    /**
     * Size is undefined.
     */
    const SIZE_UNDEFINED = -1;

    /**
     * Get size.
     */
    public function getSize(): int;

    /**
     * Get informations about a file.
     */
    public function getStatistic(): array;

    /**
     * Get last access time of file.
     */
    public function getATime(): int;

    /**
     * Get inode change time of file.
     */
    public function getCTime(): int;

    /**
     * Get file modification time.
     */
    public function getMTime(): int;

    /**
     * Get file group.
     */
    public function getGroup(): int;

    /**
     * Get file owner.
     */
    public function getOwner(): int;

    /**
     * Get file permissions.
     */
    public function getPermissions(): int;

    /**
     * Check if the file is readable.
     */
    public function isReadable(): bool;

    /**
     * Check if the file is writable.
     */
    public function isWritable(): bool;

    /**
     * Check if the file is executable.
     */
    public function isExecutable(): bool;

    /**
     * Clear file status cache.
     */
    public function clearStatisticCache();

    /**
     * Clear all files status cache.
     */
    public static function clearAllStatisticCaches();
}
