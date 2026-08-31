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

use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Extends the \RecursiveDirectoryIterator to support relative paths.
 *
 * @author Victor Berchet <victor@suumit.com>
 *
 * @extends \RecursiveDirectoryIterator<string, SplFileInfo>
 */
class RecursiveDirectoryIterator extends \RecursiveDirectoryIterator
{
    private bool $ignoreUnreadableDirs;
    private bool $ignoreFirstRewind = true;

    // these 3 properties take part of the performance optimization to avoid redoing the same work in all iterations
    private string $rootPath;
    private string $subPath;
    private string $directorySeparator = '/';

    /**
     * @throws \RuntimeException
     */
    public function __construct(string $path, int $flags, bool $ignoreUnreadableDirs = false)
    {
        if ($flags & (self::CURRENT_AS_PATHNAME | self::CURRENT_AS_SELF)) {
            throw new \RuntimeException('This iterator only support returning current as fileinfo.');
        }

        parent::__construct($path, $flags);
        $this->ignoreUnreadableDirs = $ignoreUnreadableDirs;
        $this->rootPath = $path;
        if ('/' !== \DIRECTORY_SEPARATOR && !($flags & self::UNIX_PATHS)) {
            $this->directorySeparator = \DIRECTORY_SEPARATOR;
        }
    }

    /**
     * Return an instance of SplFileInfo with support for relative paths.
     */
    public function current(): SplFileInfo
    {
        // the logic here avoids redoing the same work in all iterations

        if (!isset($this->subPath)) {
            $this->subPath = $this->getSubPath();
        }
        $subPathname = $this->subPath;
        if ('' !== $subPathname) {
            $subPathname .= $this->directorySeparator;
        }
        $subPathname .= $this->getFilename();
        $basePath = $this->rootPath;

        if ('/' !== $basePath && !str_ends_with($basePath, $this->directorySeparator) && !str_ends_with($basePath, '/')) {
            $basePath .= $this->directorySeparator;
        }

        return new SplFileInfo($basePath.$subPathname, $this->subPath, $subPathname);
    }

    public function hasChildren(bool $allowLinks = false): bool
    {
        $hasChildren = parent::hasChildren($allowLinks);

        if (!$hasChildren || !$this->ignoreUnreadableDirs) {
            return $hasChildren;
        }

        try {
            parent::getChildren();

            return true;
        } catch (\UnexpectedValueException) {
            // If directory is unreadable and finder is set to ignore it, skip children
            return false;
        }
    }

    /**
     * @throws AccessDeniedException
     */
    public function getChildren(): \RecursiveDirectoryIterator
    {
        try {
            $children = parent::getChildren();

            if ($children instanceof self) {
                // parent method will call the constructor with default arguments, so unreadable dirs won't be ignored anymore
                $children->ignoreUnreadableDirs = $this->ignoreUnreadableDirs;

                // performance optimization to avoid redoing the same work in all children
                $children->rootPath = $this->rootPath;
            }

            return $children;
        } catch (\UnexpectedValueException $e) {
            throw new AccessDeniedException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function next(): void
    {
        $this->ignoreFirstRewind = false;

        parent::next();
    }

    public function rewind(): void
    {
        // some streams like FTP are not rewindable, ignore the first rewind after creation,
        // as newly created DirectoryIterator does not need to be rewound
        if ($this->ignoreFirstRewind) {
            $this->ignoreFirstRewind = false;

            return;
        }

        parent::rewind();
    }
}
