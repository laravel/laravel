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
 * Interface \Hoa\Stream\IStream\Lockable.
 *
 * Interface for lockable input/output.
 *
 * @license    New BSD License
 */
interface StreamLockable extends IStream
{
    /**
     * Acquire a shared lock (reader).
     *
     * @const int
     */
    const LOCK_SHARED = \LOCK_SH;

    /**
     * Acquire an exclusive lock (writer).
     *
     * @const int
     */
    const LOCK_EXCLUSIVE = \LOCK_EX;

    /**
     * Release a lock (shared or exclusive).
     *
     * @const int
     */
    const LOCK_RELEASE = \LOCK_UN;

    /**
     * If we do not want $this->lock() to block while locking.
     *
     * @const int
     */
    const LOCK_NO_BLOCK = \LOCK_NB;

    /**
     * Portable advisory locking.
     * Should take a look at stream_supports_lock().
     *
     * @param int $operation operation, use the self::LOCK_* constants
     *
     * @return bool
     */
    public function lock(int $operation): bool;
}
