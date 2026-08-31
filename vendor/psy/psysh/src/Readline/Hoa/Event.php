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
 * Events are asynchronous at registration, anonymous at use (until we
 * receive a bucket) and useful to largely spread data through components
 * without any known connection between them.
 */
class Event
{
    /**
     * Event ID key.
     */
    const KEY_EVENT = 0;

    /**
     * Source object key.
     */
    const KEY_SOURCE = 1;

    /**
     * Static register of all observable objects, i.e. `Hoa\Event\Source`
     * object, i.e. object that can send event.
     */
    private static $_register = [];

    /**
     * Collection of callables, i.e. observer objects.
     */
    protected $_callable = [];

    /**
     * Privatize the constructor.
     */
    private function __construct()
    {
        return;
    }

    /**
     * Manage multiton of events, with the principle of asynchronous
     * attachments.
     */
    public static function getEvent(string $eventId): self
    {
        if (!isset(self::$_register[$eventId][self::KEY_EVENT])) {
            self::$_register[$eventId] = [
                self::KEY_EVENT  => new self(),
                self::KEY_SOURCE => null,
            ];
        }

        return self::$_register[$eventId][self::KEY_EVENT];
    }

    /**
     * Declares a new object in the observable collection.
     * Note: Hoa's libraries use `hoa://Event/anID` for their observable objects.
     */
    public static function register(string $eventId, /* Source|string */ $source)
    {
        if (true === self::eventExists($eventId)) {
            throw new EventException('Cannot redeclare an event with the same ID, i.e. the event '.'ID %s already exists.', 0, $eventId);
        }

        if (\is_object($source) && !($source instanceof EventSource)) {
            throw new EventException('The source must implement \Hoa\Event\Source '.'interface; given %s.', 1, \get_class($source));
        } else {
            $reflection = new \ReflectionClass($source);

            if (false === $reflection->implementsInterface('\Psy\Readline\Hoa\EventSource')) {
                throw new EventException('The source must implement \Hoa\Event\Source '.'interface; given %s.', 2, $source);
            }
        }

        if (!isset(self::$_register[$eventId][self::KEY_EVENT])) {
            self::$_register[$eventId][self::KEY_EVENT] = new self();
        }

        self::$_register[$eventId][self::KEY_SOURCE] = $source;
    }

    /**
     * Undeclares an object in the observable collection.
     *
     * If `$hard` is set to `true, then the source and its attached callables
     * will be deleted.
     */
    public static function unregister(string $eventId, bool $hard = false)
    {
        if (false !== $hard) {
            unset(self::$_register[$eventId]);
        } else {
            self::$_register[$eventId][self::KEY_SOURCE] = null;
        }
    }

    /**
     * Attach an object to an event.
     *
     * It can be a callable or an accepted callable form (please, see the
     * `Hoa\Consistency\Xcallable` class).
     */
    public function attach($callable): self
    {
        $callable = Xcallable::from($callable);
        $this->_callable[$callable->getHash()] = $callable;

        return $this;
    }

    /**
     * Detaches an object to an event.
     *
     * Please see `self::attach` method.
     */
    public function detach($callable): self
    {
        unset($this->_callable[Xcallable::from($callable)->getHash()]);

        return $this;
    }

    /**
     * Checks if at least one callable is attached to an event.
     */
    public function isListened(): bool
    {
        return !empty($this->_callable);
    }

    /**
     * Notifies, i.e. send data to observers.
     */
    public static function notify(string $eventId, EventSource $source, EventBucket $data)
    {
        if (false === self::eventExists($eventId)) {
            throw new EventException('Event ID %s does not exist, cannot send notification.', 3, $eventId);
        }

        $data->setSource($source);
        $event = self::getEvent($eventId);

        foreach ($event->_callable as $callable) {
            $callable($data);
        }
    }

    /**
     * Checks whether an event exists.
     */
    public static function eventExists(string $eventId): bool
    {
        return
            \array_key_exists($eventId, self::$_register) &&
            self::$_register[$eventId][self::KEY_SOURCE] !== null;
    }
}
