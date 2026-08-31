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
 * Class \Hoa\Iterator\Recursive\Directory.
 *
 * Extending the SPL RecursiveDirectoryIterator class.
 */
class IteratorRecursiveDirectory extends \RecursiveDirectoryIterator
{
    /**
     * SplFileInfo classname.
     */
    protected $_splFileInfoClass = null;

    /**
     * Relative path.
     */
    protected $_relativePath = null;

    /**
     * Constructor.
     * Please, see \RecursiveDirectoryIterator::__construct() method.
     * We add the $splFileInfoClass parameter.
     */
    public function __construct(string $path, ?int $flags = null, ?string $splFileInfoClass = null)
    {
        if (null === $flags) {
            parent::__construct($path);
        } else {
            parent::__construct($path, $flags);
        }

        $this->_relativePath = $path;
        $this->setSplFileInfoClass($splFileInfoClass);

        return;
    }

    /**
     * Current.
     * Please, see \RecursiveDirectoryIterator::current() method.
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        $out = parent::current();

        if (null !== $this->_splFileInfoClass &&
            $out instanceof \SplFileInfo) {
            $out->setInfoClass($this->_splFileInfoClass);
            $out = $out->getFileInfo();

            if ($out instanceof IteratorSplFileInfo) {
                $out->setRelativePath($this->getRelativePath());
            }
        }

        return $out;
    }

    /**
     * Get children.
     * Please, see \RecursiveDirectoryIterator::getChildren() method.
     */
    #[\ReturnTypeWillChange]
    public function getChildren()
    {
        $out = parent::getChildren();
        $out->_relativePath = $this->getRelativePath();
        $out->setSplFileInfoClass($this->_splFileInfoClass);

        return $out;
    }

    /**
     * Set SplFileInfo classname.
     */
    public function setSplFileInfoClass($splFileInfoClass)
    {
        $this->_splFileInfoClass = $splFileInfoClass;
    }

    /**
     * Get relative path (if given).
     */
    public function getRelativePath(): string
    {
        return $this->_relativePath;
    }
}
