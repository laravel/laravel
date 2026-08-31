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
 * Build a callable object, i.e. `function`, `class::method`, `object->method` or
 * closure. They all have the same behaviour. This callable is an extension of
 * native PHP callable (aka callback) to integrate Hoa's structures.
 */
class Xcallable
{
    /**
     * Callback with the PHP format.
     */
    protected $_callback = null;

    /**
     * Callable hash.
     */
    protected $_hash = null;

    /**
     * Allocates a xcallable based on a callback.
     *
     * Accepted forms:
     *     * `'function'`,
     *     * `'class::method'`,
     *     * `'class', 'method'`,
     *     * `$object, 'method'`,
     *     * `$object, ''`,
     *     * `function (…) { … }`,
     *     * `['class', 'method']`,
     *     * `[$object, 'method']`.
     *
     * # Examples
     *
     * ```php
     * $toUpper = new Hoa\Consistency\Xcallable('strtoupper');
     * assert('FOO' === $toUpper('foo'));
     * ```
     *
     * # Exceptions
     *
     * A `Hoa\Consistency\Exception` exception is thrown if the callback form
     * is invalid.
     *
     * ```php,must_throw(Hoa\Consistency\Exception)
     * new Hoa\Consistency\Xcallable('Foo:');
     * ```
     */
    public function __construct($call, $able = '')
    {
        if ($call instanceof \Closure) {
            $this->_callback = $call;

            return;
        }

        if (!\is_string($able)) {
            throw new Exception('Bad callback form; the able part must be a string.', 0);
        }

        if ('' === $able) {
            if (\is_string($call)) {
                if (false === \strpos($call, '::')) {
                    if (!\function_exists($call)) {
                        throw new Exception('Bad callback form; function %s does not exist.', 1, $call);
                    }

                    $this->_callback = $call;

                    return;
                }

                list($call, $able) = \explode('::', $call);
            } elseif (\is_object($call)) {
                if ($call instanceof StreamOut) {
                    $able = null;
                } elseif (\method_exists($call, '__invoke')) {
                    $able = '__invoke';
                } else {
                    throw new Exception('Bad callback form; an object but without a known '.'method.', 2);
                }
            } elseif (\is_array($call) && isset($call[0])) {
                if (!isset($call[1])) {
                    $this->__construct($call[0]);
                    return;
                }

                $this->__construct($call[0], $call[1]);
                return;
            } else {
                throw new Exception('Bad callback form.', 3);
            }
        }

        $this->_callback = [$call, $able];

        return;
    }

    /**
     * Calls the callable.
     */
    public function __invoke(...$arguments)
    {
        $callback = $this->getValidCallback($arguments);

        return $callback(...$arguments);
    }

    /**
     * Returns a valid PHP callback.
     */
    public function getValidCallback(array &$arguments = [])
    {
        $callback = $this->_callback;
        $head = null;

        if (isset($arguments[0])) {
            $head = &$arguments[0];
        }

        // If method is undetermined, we find it (we understand event bucket and
        // stream).
        if (null !== $head &&
            \is_array($callback) &&
            null === $callback[1]) {
            if ($head instanceof EventBucket) {
                $head = $head->getData();
            }

            switch ($type = \gettype($head)) {
                case 'string':
                    if (1 === \strlen($head)) {
                        $method = 'writeCharacter';
                    } else {
                        $method = 'writeString';
                    }

                    break;

                case 'boolean':
                case 'integer':
                case 'array':
                    $method = 'write'.\ucfirst($type);

                    break;

                case 'double':
                    $method = 'writeFloat';

                    break;

                default:
                    $method = 'writeAll';
                    $head = $head."\n";
            }

            $callback[1] = $method;
        }

        return $callback;
    }

    /**
     * Computes the hash of this callable.
     *
     * Will produce:
     *     * `function#…`,
     *     * `class#…::…`,
     *     * `object(…)#…::…`,
     *     * `closure(…)`.
     */
    public function getHash(): string
    {
        if (null !== $this->_hash) {
            return $this->_hash;
        }

        $_ = &$this->_callback;

        if (\is_string($_)) {
            return $this->_hash = 'function#'.$_;
        }

        if (\is_array($_)) {
            return
                $this->_hash =
                    (\is_object($_[0])
                        ? 'object('.\spl_object_hash($_[0]).')'.
                          '#'.\get_class($_[0])
                        : 'class#'.$_[0]).
                    '::'.
                    (null !== $_[1]
                        ? $_[1]
                        : '???');
        }

        return $this->_hash = 'closure('.\spl_object_hash($_).')';
    }

    /**
     * The string representation of a callable is its hash.
     */
    public function __toString(): string
    {
        return $this->getHash();
    }

    /**
     * Hoa's xcallable() helper.
     */
    public static function from($call, $able = '')
    {
        if ($call instanceof self) {
            return $call;
        }

        return new self($call, $able);
    }
}
