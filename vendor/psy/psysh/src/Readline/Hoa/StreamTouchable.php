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
 * Interface \Hoa\Stream\IStream\Touchable.
 *
 * Interface for touchable input/output.
 */
interface StreamTouchable extends IStream
{
    /**
     * Overwrite file if already exists.
     */
    const OVERWRITE = true;

    /**
     * Do not overwrite file if already exists.
     */
    const DO_NOT_OVERWRITE = false;

    /**
     * Make directory if does not exist.
     */
    const MAKE_DIRECTORY = true;

    /**
     * Do not make directory if does not exist.
     */
    const DO_NOT_MAKE_DIRECTORY = false;

    /**
     * Set access and modification time of file.
     */
    public function touch(int $time = -1, int $atime = -1): bool;

    /**
     * Copy file.
     * Return the destination file path if succeed, false otherwise.
     */
    public function copy(string $to, bool $force = self::DO_NOT_OVERWRITE): bool;

    /**
     * Move a file.
     */
    public function move(
        string $name,
        bool $force = self::DO_NOT_OVERWRITE,
        bool $mkdir = self::DO_NOT_MAKE_DIRECTORY
    ): bool;

    /**
     * Delete a file.
     */
    public function delete(): bool;

    /**
     * Change file group.
     */
    public function changeGroup($group): bool;

    /**
     * Change file mode.
     */
    public function changeMode(int $mode): bool;

    /**
     * Change file owner.
     */
    public function changeOwner($user): bool;

    /**
     * Change the current umask.
     */
    public static function umask(?int $umask = null): int;
}
