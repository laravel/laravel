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
 * Class \Hoa\Console\Readline\Autocompleter\Path.
 *
 * Path autocompleter.
 */
class AutocompleterPath implements Autocompleter
{
    /**
     * Root is the current working directory.
     */
    const PWD = null;

    /**
     * Root.
     */
    protected $_root = null;

    /**
     * Iterator factory. Please, see the self::setIteratorFactory method.
     */
    protected $_iteratorFactory = null;

    /**
     * Constructor.
     */
    public function __construct(
        ?string $root = null,
        ?\Closure $iteratorFactory = null
    ) {
        if (null === $root) {
            $root = static::PWD;
        }

        if (null !== $root) {
            $this->setRoot($root);
        }

        if (null !== $iteratorFactory) {
            $this->setIteratorFactory($iteratorFactory);
        }
    }

    /**
     * Complete a word.
     * Returns null for no word, a full-word or an array of full-words.
     */
    public function complete(&$prefix)
    {
        $root = $this->getRoot();

        if (static::PWD === $root) {
            $root = \getcwd();
        }

        $path = $root.\DIRECTORY_SEPARATOR.$prefix;

        if (!\is_dir($path)) {
            $path = \dirname($path).\DIRECTORY_SEPARATOR;
            $prefix = \basename($prefix);
        } else {
            $prefix = null;
        }

        $iteratorFactory = $this->getIteratorFactory() ?:
                               static::getDefaultIteratorFactory();

        try {
            $iterator = $iteratorFactory($path);
            $out = [];
            $length = \mb_strlen($prefix);

            foreach ($iterator as $fileinfo) {
                $filename = $fileinfo->getFilename();

                if (null === $prefix ||
                    (\mb_substr($filename, 0, $length) === $prefix)) {
                    if ($fileinfo->isDir()) {
                        $out[] = $filename.'/';
                    } else {
                        $out[] = $filename;
                    }
                }
            }
        } catch (\Exception $e) {
            return null;
        }

        $count = \count($out);

        if (1 === $count) {
            return $out[0];
        }

        if (0 === $count) {
            return null;
        }

        return $out;
    }

    /**
     * Get definition of a word.
     */
    public function getWordDefinition(): string
    {
        return '/?[\w\d\\_\-\.]+(/[\w\d\\_\-\.]*)*';
    }

    /**
     * Set root.
     */
    public function setRoot(string $root)
    {
        $old = $this->_root;
        $this->_root = $root;

        return $old;
    }

    /**
     * Get root.
     */
    public function getRoot()
    {
        return $this->_root;
    }

    /**
     * Set iterator factory (a finder).
     */
    public function setIteratorFactory(\Closure $iteratorFactory)
    {
        $old = $this->_iteratorFactory;
        $this->_iteratorFactory = $iteratorFactory;

        return $old;
    }

    /**
     * Get iterator factory.
     */
    public function getIteratorFactory()
    {
        return $this->_iteratorFactory;
    }

    /**
     * Get default iterator factory (based on \DirectoryIterator).
     */
    public static function getDefaultIteratorFactory()
    {
        return function ($path) {
            return new \DirectoryIterator($path);
        };
    }
}
