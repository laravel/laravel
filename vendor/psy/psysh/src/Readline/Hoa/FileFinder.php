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
 * Class \Hoa\File\Finder.
 *
 * This class allows to find files easily by using filters and flags.
 */
class FileFinder implements \IteratorAggregate
{
    /**
     * SplFileInfo classname.
     */
    protected $_splFileInfo = \SplFileInfo::class;

    /**
     * Paths where to look for.
     */
    protected $_paths = [];

    /**
     * Max depth in recursion.
     */
    protected $_maxDepth = -1;

    /**
     * Filters.
     */
    protected $_filters = [];

    /**
     * Flags.
     */
    protected $_flags = -1;

    /**
     * Types of files to handle.
     */
    protected $_types = [];

    /**
     * What comes first: parent or child?
     */
    protected $_first = -1;

    /**
     * Sorts.
     */
    protected $_sorts = [];

    /**
     * Initialize.
     */
    public function __construct()
    {
        $this->_flags = IteratorFileSystem::KEY_AS_PATHNAME
                        | IteratorFileSystem::CURRENT_AS_FILEINFO
                        | IteratorFileSystem::SKIP_DOTS;
        $this->_first = \RecursiveIteratorIterator::SELF_FIRST;

        return;
    }

    /**
     * Select a directory to scan.
     */
    public function in($paths): self
    {
        if (!\is_array($paths)) {
            $paths = [$paths];
        }

        foreach ($paths as $path) {
            if (1 === \preg_match('/[\*\?\[\]]/', $path)) {
                $iterator = new \CallbackFilterIterator(
                    new \GlobIterator(\rtrim($path, \DIRECTORY_SEPARATOR)),
                    function ($current) {
                        return $current->isDir();
                    }
                );

                foreach ($iterator as $fileInfo) {
                    $this->_paths[] = $fileInfo->getPathname();
                }
            } else {
                $this->_paths[] = $path;
            }
        }

        return $this;
    }

    /**
     * Set max depth for recursion.
     */
    public function maxDepth(int $depth): self
    {
        $this->_maxDepth = $depth;

        return $this;
    }

    /**
     * Include files in the result.
     */
    public function files(): self
    {
        $this->_types[] = 'file';

        return $this;
    }

    /**
     * Include directories in the result.
     */
    public function directories(): self
    {
        $this->_types[] = 'dir';

        return $this;
    }

    /**
     * Include links in the result.
     */
    public function links(): self
    {
        $this->_types[] = 'link';

        return $this;
    }

    /**
     * Follow symbolink links.
     */
    public function followSymlinks(bool $flag = true): self
    {
        if (true === $flag) {
            $this->_flags ^= IteratorFileSystem::FOLLOW_SYMLINKS;
        } else {
            $this->_flags |= IteratorFileSystem::FOLLOW_SYMLINKS;
        }

        return $this;
    }

    /**
     * Include files that match a regex.
     * Example:
     *     $this->name('#\.php$#');.
     */
    public function name(string $regex): self
    {
        $this->_filters[] = function (\SplFileInfo $current) use ($regex) {
            return 0 !== \preg_match($regex, $current->getBasename());
        };

        return $this;
    }

    /**
     * Exclude directories that match a regex.
     * Example:
     *      $this->notIn('#^\.(git|hg)$#');.
     */
    public function notIn(string $regex): self
    {
        $this->_filters[] = function (\SplFileInfo $current) use ($regex) {
            foreach (\explode(\DIRECTORY_SEPARATOR, $current->getPathname()) as $part) {
                if (0 !== \preg_match($regex, $part)) {
                    return false;
                }
            }

            return true;
        };

        return $this;
    }

    /**
     * Include files that respect a certain size.
     * The size is a string of the form:
     *     operator number unit
     * where
     *     • operator could be: <, <=, >, >= or =;
     *     • number is a positive integer;
     *     • unit could be: b (default), Kb, Mb, Gb, Tb, Pb, Eb, Zb, Yb.
     * Example:
     *     $this->size('>= 12Kb');.
     */
    public function size(string $size): self
    {
        if (0 === \preg_match('#^(<|<=|>|>=|=)\s*(\d+)\s*((?:[KMGTPEZY])b)?$#', $size, $matches)) {
            return $this;
        }

        $number = (float) ($matches[2]);
        $unit = $matches[3] ?? 'b';
        $operator = $matches[1];

        switch ($unit) {
            case 'b':
                break;

            // kilo
            case 'Kb':
                $number <<= 10;

                break;

            // mega.
            case 'Mb':
                $number <<= 20;

                break;

            // giga.
            case 'Gb':
                $number <<= 30;

                break;

            // tera.
            case 'Tb':
                $number *= 1099511627776;

                break;

            // peta.
            case 'Pb':
                $number *= 1024 ** 5;

                break;

            // exa.
            case 'Eb':
                $number *= 1024 ** 6;

                break;

            // zetta.
            case 'Zb':
                $number *= 1024 ** 7;

                break;

            // yota.
            case 'Yb':
                $number *= 1024 ** 8;

                break;
        }

        $filter = null;

        switch ($operator) {
            case '<':
                $filter = function (\SplFileInfo $current) use ($number) {
                    return $current->getSize() < $number;
                };

                break;

            case '<=':
                $filter = function (\SplFileInfo $current) use ($number) {
                    return $current->getSize() <= $number;
                };

                break;

            case '>':
                $filter = function (\SplFileInfo $current) use ($number) {
                    return $current->getSize() > $number;
                };

                break;

            case '>=':
                $filter = function (\SplFileInfo $current) use ($number) {
                    return $current->getSize() >= $number;
                };

                break;

            case '=':
                $filter = function (\SplFileInfo $current) use ($number) {
                    return $current->getSize() === $number;
                };

                break;
        }

        $this->_filters[] = $filter;

        return $this;
    }

    /**
     * Whether we should include dots or not (respectively . and ..).
     */
    public function dots(bool $flag = true): self
    {
        if (true === $flag) {
            $this->_flags ^= IteratorFileSystem::SKIP_DOTS;
        } else {
            $this->_flags |= IteratorFileSystem::SKIP_DOTS;
        }

        return $this;
    }

    /**
     * Include files that are owned by a certain owner.
     */
    public function owner(int $owner): self
    {
        $this->_filters[] = function (\SplFileInfo $current) use ($owner) {
            return $current->getOwner() === $owner;
        };

        return $this;
    }

    /**
     * Format date.
     * Date can have the following syntax:
     *     date
     *     since date
     *     until date
     * If the date does not have the “ago” keyword, it will be added.
     * Example: “42 hours” is equivalent to “since 42 hours” which is equivalent
     * to “since 42 hours ago”.
     */
    protected function formatDate(string $date, &$operator): int
    {
        $operator = -1;

        if (0 === \preg_match('#\bago\b#', $date)) {
            $date .= ' ago';
        }

        if (0 !== \preg_match('#^(since|until)\b(.+)$#', $date, $matches)) {
            $time = \strtotime($matches[2]);

            if ('until' === $matches[1]) {
                $operator = 1;
            }
        } else {
            $time = \strtotime($date);
        }

        return $time;
    }

    /**
     * Include files that have been changed from a certain date.
     * Example:
     *     $this->changed('since 13 days');.
     */
    public function changed(string $date): self
    {
        $time = $this->formatDate($date, $operator);

        if (-1 === $operator) {
            $this->_filters[] = function (\SplFileInfo $current) use ($time) {
                return $current->getCTime() >= $time;
            };
        } else {
            $this->_filters[] = function (\SplFileInfo $current) use ($time) {
                return $current->getCTime() < $time;
            };
        }

        return $this;
    }

    /**
     * Include files that have been modified from a certain date.
     * Example:
     *     $this->modified('since 13 days');.
     */
    public function modified(string $date): self
    {
        $time = $this->formatDate($date, $operator);

        if (-1 === $operator) {
            $this->_filters[] = function (\SplFileInfo $current) use ($time) {
                return $current->getMTime() >= $time;
            };
        } else {
            $this->_filters[] = function (\SplFileInfo $current) use ($time) {
                return $current->getMTime() < $time;
            };
        }

        return $this;
    }

    /**
     * Add your own filter.
     * The callback will receive 3 arguments: $current, $key and $iterator. It
     * must return a boolean: true to include the file, false to exclude it.
     * Example:
     *     // Include files that are readable
     *     $this->filter(function ($current) {
     *         return $current->isReadable();
     *     });.
     */
    public function filter($callback): self
    {
        $this->_filters[] = $callback;

        return $this;
    }

    /**
     * Sort result by name.
     * If \Collator exists (from ext/intl), the $locale argument will be used
     * for its constructor. Else, strcmp() will be used.
     * Example:
     *     $this->sortByName('fr_FR');.
     */
    public function sortByName(string $locale = 'root'): self
    {
        if (true === \class_exists('Collator', false)) {
            $collator = new \Collator($locale);

            $this->_sorts[] = function (\SplFileInfo $a, \SplFileInfo $b) use ($collator) {
                return $collator->compare($a->getPathname(), $b->getPathname());
            };
        } else {
            $this->_sorts[] = function (\SplFileInfo $a, \SplFileInfo $b) {
                return \strcmp($a->getPathname(), $b->getPathname());
            };
        }

        return $this;
    }

    /**
     * Sort result by size.
     * Example:
     *     $this->sortBySize();.
     */
    public function sortBySize(): self
    {
        $this->_sorts[] = function (\SplFileInfo $a, \SplFileInfo $b) {
            return $a->getSize() < $b->getSize();
        };

        return $this;
    }

    /**
     * Add your own sort.
     * The callback will receive 2 arguments: $a and $b. Please see the uasort()
     * function.
     * Example:
     *     // Sort files by their modified time.
     *     $this->sort(function ($a, $b) {
     *         return $a->getMTime() < $b->getMTime();
     *     });.
     */
    public function sort($callable): self
    {
        $this->_sorts[] = $callable;

        return $this;
    }

    /**
     * Child comes first when iterating.
     */
    public function childFirst(): self
    {
        $this->_first = \RecursiveIteratorIterator::CHILD_FIRST;

        return $this;
    }

    /**
     * Get the iterator.
     */
    public function getIterator()
    {
        $_iterator = new \AppendIterator();
        $types = $this->getTypes();

        if (!empty($types)) {
            $this->_filters[] = function (\SplFileInfo $current) use ($types) {
                return \in_array($current->getType(), $types);
            };
        }

        $maxDepth = $this->getMaxDepth();
        $splFileInfo = $this->getSplFileInfo();

        foreach ($this->getPaths() as $path) {
            if (1 === $maxDepth) {
                $iterator = new \IteratorIterator(
                    new IteratorRecursiveDirectory(
                        $path,
                        $this->getFlags(),
                        $splFileInfo
                    ),
                    $this->getFirst()
                );
            } else {
                $iterator = new \RecursiveIteratorIterator(
                    new IteratorRecursiveDirectory(
                        $path,
                        $this->getFlags(),
                        $splFileInfo
                    ),
                    $this->getFirst()
                );

                if (1 < $maxDepth) {
                    $iterator->setMaxDepth($maxDepth - 1);
                }
            }

            $_iterator->append($iterator);
        }

        foreach ($this->getFilters() as $filter) {
            $_iterator = new \CallbackFilterIterator(
                $_iterator,
                $filter
            );
        }

        $sorts = $this->getSorts();

        if (empty($sorts)) {
            return $_iterator;
        }

        $array = \iterator_to_array($_iterator);

        foreach ($sorts as $sort) {
            \uasort($array, $sort);
        }

        return new \ArrayIterator($array);
    }

    /**
     * Set SplFileInfo classname.
     */
    public function setSplFileInfo(string $splFileInfo): string
    {
        $old = $this->_splFileInfo;
        $this->_splFileInfo = $splFileInfo;

        return $old;
    }

    /**
     * Get SplFileInfo classname.
     */
    public function getSplFileInfo(): string
    {
        return $this->_splFileInfo;
    }

    /**
     * Get all paths.
     */
    protected function getPaths(): array
    {
        return $this->_paths;
    }

    /**
     * Get max depth.
     */
    public function getMaxDepth(): int
    {
        return $this->_maxDepth;
    }

    /**
     * Get types.
     */
    public function getTypes(): array
    {
        return $this->_types;
    }

    /**
     * Get filters.
     */
    protected function getFilters(): array
    {
        return $this->_filters;
    }

    /**
     * Get sorts.
     */
    protected function getSorts(): array
    {
        return $this->_sorts;
    }

    /**
     * Get flags.
     */
    public function getFlags(): int
    {
        return $this->_flags;
    }

    /**
     * Get first.
     */
    public function getFirst(): int
    {
        return $this->_first;
    }
}
