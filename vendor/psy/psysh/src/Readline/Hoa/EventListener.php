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
 * A contrario of events, listeners are synchronous, identified at use and
 * useful for close interactions between one or some components.
 */
class EventListener
{
    /**
     * Source of listener (for `Hoa\Event\Bucket`).
     */
    protected $_source = null;

    /**
     * All listener IDs and associated listeners.
     */
    protected $_callables = [];

    /**
     * Build a listener.
     */
    public function __construct(EventListenable $source, array $ids)
    {
        $this->_source = $source;
        $this->addIds($ids);

        return;
    }

    /**
     * Adds acceptable ID (or reset).
     */
    public function addIds(array $ids)
    {
        foreach ($ids as $id) {
            $this->_callables[$id] = [];
        }
    }

    /**
     * Attaches a callable to a listenable component.
     */
    public function attach(string $listenerId, $callable): self
    {
        if (false === $this->listenerExists($listenerId)) {
            throw new EventException('Cannot listen %s because it is not defined.', 0, $listenerId);
        }

        $callable = Xcallable::from($callable);
        $this->_callables[$listenerId][$callable->getHash()] = $callable;

        return $this;
    }

    /**
     * Detaches a callable from a listenable component.
     */
    public function detach(string $listenerId, $callable): self
    {
        unset($this->_callables[$listenerId][Xcallable::from($callable)->getHash()]);

        return $this;
    }

    /**
     * Detaches all callables from a listenable component.
     */
    public function detachAll(string $listenerId): self
    {
        unset($this->_callables[$listenerId]);

        return $this;
    }

    /**
     * Checks if a listener exists.
     */
    public function listenerExists(string $listenerId): bool
    {
        return \array_key_exists($listenerId, $this->_callables);
    }

    /**
     * Sends/fires a bucket to a listener.
     */
    public function fire(string $listenerId, EventBucket $data): array
    {
        if (false === $this->listenerExists($listenerId)) {
            throw new EventException('Cannot fire on %s because it is not defined.', 1, $listenerId);
        }

        $data->setSource($this->_source);
        $out = [];

        foreach ($this->_callables[$listenerId] as $callable) {
            $out[] = $callable($data);
        }

        return $out;
    }
}
