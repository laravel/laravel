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

/**
 * FileTypeFilterIterator only keeps files, directories, or both.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @extends \FilterIterator<string, \SplFileInfo>
 */
class FileTypeFilterIterator extends \FilterIterator
{
    public const ONLY_FILES = 1;
    public const ONLY_DIRECTORIES = 2;

    /**
     * @param \Iterator<string, \SplFileInfo> $iterator The Iterator to filter
     * @param int                             $mode     The mode (self::ONLY_FILES or self::ONLY_DIRECTORIES)
     */
    public function __construct(
        \Iterator $iterator,
        private int $mode,
    ) {
        parent::__construct($iterator);
    }

    /**
     * Filters the iterator values.
     */
    public function accept(): bool
    {
        $fileinfo = $this->current();
        if (self::ONLY_DIRECTORIES === (self::ONLY_DIRECTORIES & $this->mode) && $fileinfo->isFile()) {
            return false;
        } elseif (self::ONLY_FILES === (self::ONLY_FILES & $this->mode) && $fileinfo->isDir()) {
            return false;
        }

        return true;
    }
}
