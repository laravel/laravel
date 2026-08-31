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
 * Class \Hoa\Stream\Context.
 *
 * Make a multiton of stream contexts.
 */
class StreamContext
{
    /**
     * Context ID.
     */
    protected $_id = null;

    /**
     * @var resource
     */
    protected $_context;

    /**
     * Multiton.
     */
    protected static $_instances = [];

    /**
     * Construct a context.
     */
    protected function __construct($id)
    {
        $this->_id = $id;
        $this->_context = \stream_context_create();

        return;
    }

    /**
     * Multiton.
     */
    public static function getInstance(string $id): self
    {
        if (false === static::contextExists($id)) {
            static::$_instances[$id] = new self($id);
        }

        return static::$_instances[$id];
    }

    /**
     * Get context ID.
     */
    public function getId(): string
    {
        return $this->_id;
    }

    /**
     * Check if a context exists.
     */
    public static function contextExists(string $id): bool
    {
        return \array_key_exists($id, static::$_instances);
    }

    /**
     * Set options.
     * Please, see http://php.net/context.
     */
    public function setOptions(array $options): bool
    {
        return \stream_context_set_option($this->getContext(), $options);
    }

    /**
     * Set parameters.
     * Please, see http://php.net/context.params.
     */
    public function setParameters(array $parameters): bool
    {
        return \stream_context_set_params($this->getContext(), $parameters);
    }

    /**
     * Get options.
     */
    public function getOptions(): array
    {
        return \stream_context_get_options($this->getContext());
    }

    /**
     * Get parameters.
     */
    public function getParameters(): array
    {
        return \stream_context_get_params($this->getContext());
    }

    /**
     * Get context as a resource.
     */
    public function getContext()
    {
        return $this->_context;
    }
}
