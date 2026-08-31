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
 * Abstract class for all `hoa://`'s nodes.
 */
class ProtocolNode implements \ArrayAccess, \IteratorAggregate
{
    /**
     * Node's name.
     */
    protected $_name = null;

    /**
     * Path for the `reach` method.
     */
    protected $_reach = null;

    /**
     * Children of the node.
     */
    private $_children = [];

    /**
     * Construct a protocol's node.
     * If it is not a data object (i.e. if it does not extend this class to
     * overload the `$_name` attribute), we can set the `$_name` attribute
     * dynamically. This is useful to create a node on-the-fly.
     */
    public function __construct(?string $name = null, ?string $reach = null, array $children = [])
    {
        if (null !== $name) {
            $this->_name = $name;
        }

        if (null !== $reach) {
            $this->_reach = $reach;
        }

        foreach ($children as $child) {
            $this[] = $child;
        }

        return;
    }

    /**
     * Add a node.
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($name, $node)
    {
        if (!($node instanceof self)) {
            throw new ProtocolException('Protocol node must extend %s.', 0, __CLASS__);
        }

        if (empty($name)) {
            $name = $node->getName();
        }

        if (empty($name)) {
            throw new ProtocolException('Cannot add a node to the `hoa://` protocol without a name.', 1);
        }

        $this->_children[$name] = $node;
    }

    /**
     * Get a specific node.
     */
    public function offsetGet($name): self
    {
        if (!isset($this[$name])) {
            throw new ProtocolException('Node %s does not exist.', 2, $name);
        }

        return $this->_children[$name];
    }

    /**
     * Check if a node exists.
     */
    public function offsetExists($name): bool
    {
        return true === \array_key_exists($name, $this->_children);
    }

    /**
     * Remove a node.
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($name)
    {
        unset($this->_children[$name]);
    }

    /**
     * Resolve a path, i.e. iterate the nodes tree and reach the queue of
     * the path.
     */
    protected function _resolve(string $path, &$accumulator, ?string $id = null)
    {
        if (\substr($path, 0, 6) === 'hoa://') {
            $path = \substr($path, 6);
        }

        if (empty($path)) {
            return null;
        }

        if (null === $accumulator) {
            $accumulator = [];
            $posId = \strpos($path, '#');

            if (false !== $posId) {
                $id = \substr($path, $posId + 1);
                $path = \substr($path, 0, $posId);
            } else {
                $id = null;
            }
        }

        $path = \trim($path, '/');
        $pos = \strpos($path, '/');

        if (false !== $pos) {
            $next = \substr($path, 0, $pos);
        } else {
            $next = $path;
        }

        if (isset($this[$next])) {
            if (false === $pos) {
                if (null === $id) {
                    $this->_resolveChoice($this[$next]->reach(), $accumulator);

                    return true;
                }

                $accumulator = null;

                return $this[$next]->reachId($id);
            }

            $tnext = $this[$next];
            $this->_resolveChoice($tnext->reach(), $accumulator);

            return $tnext->_resolve(\substr($path, $pos + 1), $accumulator, $id);
        }

        $this->_resolveChoice($this->reach($path), $accumulator);

        return true;
    }

    /**
     * Resolve choices, i.e. a reach value has a “;”.
     */
    protected function _resolveChoice($reach, &$accumulator)
    {
        if (null === $reach) {
            $reach = '';
        }

        if (empty($accumulator)) {
            $accumulator = \explode(';', $reach);

            return;
        }

        if (false === \strpos($reach, ';')) {
            if (false !== $pos = \strrpos($reach, "\r")) {
                $reach = \substr($reach, $pos + 1);

                foreach ($accumulator as &$entry) {
                    $entry = null;
                }
            }

            foreach ($accumulator as &$entry) {
                $entry .= $reach;
            }

            return;
        }

        $choices = \explode(';', $reach);
        $ref = $accumulator;
        $accumulator = [];

        foreach ($choices as $choice) {
            if (false !== $pos = \strrpos($choice, "\r")) {
                $choice = \substr($choice, $pos + 1);

                foreach ($ref as $entry) {
                    $accumulator[] = $choice;
                }
            } else {
                foreach ($ref as $entry) {
                    $accumulator[] = $entry.$choice;
                }
            }
        }

        unset($ref);

        return;
    }

    /**
     * Queue of the node.
     * Generic one. Must be overrided in children classes.
     */
    public function reach(?string $queue = null)
    {
        return empty($queue) ? $this->_reach : $queue;
    }

    /**
     * ID of the component.
     * Generic one. Should be overrided in children classes.
     */
    public function reachId(string $id)
    {
        throw new ProtocolException('The node %s has no ID support (tried to reach #%s).', 4, [$this->getName(), $id]);
    }

    /**
     * Set a new reach value.
     */
    public function setReach(string $reach)
    {
        $old = $this->_reach;
        $this->_reach = $reach;

        return $old;
    }

    /**
     * Get node's name.
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Get reach's root.
     */
    protected function getReach()
    {
        return $this->_reach;
    }

    /**
     * Get an iterator.
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->_children);
    }

    /**
     * Get root the protocol.
     */
    public static function getRoot(): Protocol
    {
        return Protocol::getInstance();
    }

    /**
     * Print a tree of component.
     */
    public function __toString(): string
    {
        static $i = 0;

        $out = \str_repeat('  ', $i).$this->getName()."\n";

        foreach ($this as $node) {
            ++$i;
            $out .= $node;
            --$i;
        }

        return $out;
    }
}
