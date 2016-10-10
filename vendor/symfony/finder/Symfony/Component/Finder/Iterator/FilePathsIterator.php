<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Finder\Iterator;

use Symfony\Component\Finder\SplFileInfo;

/**
 * Iterate over shell command result.
 *
 * @author Jean-François Simon <contact@jfsimon.fr>
 */
class FilePathsIterator extends \ArrayIterator
{
    /**
     * @var string
     */
    private $baseDir;

    /**
     * @var int
     */
    private $baseDirLength;

    /**
     * @var string
     */
    private $subPath;

    /**
     * @var string
     */
    private $subPathname;

    /**
     * @var SplFileInfo
     */
    private $current;

    /**
     * @param array  $paths   List of paths returned by shell command
     * @param string $baseDir Base dir for relative path building
     */
    public function __construct(array $paths, $baseDir)
    {
        $this->baseDir       = $baseDir;
        $this->baseDirLength = strlen($baseDir);

        parent::__construct($paths);
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($name, array $arguments)
    {
        return call_user_func_array(array($this->current(), $name), $arguments);
    }

    /**
     * Return an instance of SplFileInfo with support for relative paths.
     *
     * @return SplFileInfo File information
     */
    public function current()
    {
        return $this->current;
    }

    /**
     * @return string
     */
    public function key()
    {
        return $this->current->getPathname();
    }

    public function next()
    {
        parent::next();
        $this->buildProperties();
    }

    public function rewind()
    {
        parent::rewind();
        $this->buildProperties();
    }

    /**
     * @return string
     */
    public function getSubPath()
    {
        return $this->subPath;
    }

    /**
     * @return string
     */
    public function getSubPathname()
    {
        return $this->subPathname;
    }

    private function buildProperties()
    {
        $absolutePath = parent::current();

        if ($this->baseDir === substr($absolutePath, 0, $this->baseDirLength)) {
            $this->subPathname = ltrim(substr($absolutePath, $this->baseDirLength), '/\\');
            $dir = dirname($this->subPathname);
            $this->subPath = '.' === $dir ? '' : $dir;
        } else {
            $this->subPath = $this->subPathname = '';
        }

        $this->current = new SplFileInfo(parent::current(), $this->subPath, $this->subPathname);
    }
}
