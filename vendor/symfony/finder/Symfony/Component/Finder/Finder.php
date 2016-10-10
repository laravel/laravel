<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Finder;

use Symfony\Component\Finder\Adapter\AdapterInterface;
use Symfony\Component\Finder\Adapter\GnuFindAdapter;
use Symfony\Component\Finder\Adapter\BsdFindAdapter;
use Symfony\Component\Finder\Adapter\PhpAdapter;
use Symfony\Component\Finder\Exception\ExceptionInterface;

/**
 * Finder allows to build rules to find files and directories.
 *
 * It is a thin wrapper around several specialized iterator classes.
 *
 * All rules may be invoked several times.
 *
 * All methods return the current Finder object to allow easy chaining:
 *
 * $finder = Finder::create()->files()->name('*.php')->in(__DIR__);
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @api
 */
class Finder implements \IteratorAggregate, \Countable
{
    const IGNORE_VCS_FILES = 1;
    const IGNORE_DOT_FILES = 2;

    private $mode        = 0;
    private $names       = array();
    private $notNames    = array();
    private $exclude     = array();
    private $filters     = array();
    private $depths      = array();
    private $sizes       = array();
    private $followLinks = false;
    private $sort        = false;
    private $ignore      = 0;
    private $dirs        = array();
    private $dates       = array();
    private $iterators   = array();
    private $contains    = array();
    private $notContains = array();
    private $adapters    = array();
    private $paths       = array();
    private $notPaths    = array();
    private $ignoreUnreadableDirs = false;

    private static $vcsPatterns = array('.svn', '_svn', 'CVS', '_darcs', '.arch-params', '.monotone', '.bzr', '.git', '.hg');

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->ignore = static::IGNORE_VCS_FILES | static::IGNORE_DOT_FILES;

        $this
            ->addAdapter(new GnuFindAdapter())
            ->addAdapter(new BsdFindAdapter())
            ->addAdapter(new PhpAdapter(), -50)
            ->setAdapter('php')
        ;
    }

    /**
     * Creates a new Finder.
     *
     * @return Finder A new Finder instance
     *
     * @api
     */
    public static function create()
    {
        return new static();
    }

    /**
     * Registers a finder engine implementation.
     *
     * @param AdapterInterface $adapter  An adapter instance
     * @param int              $priority Highest is selected first
     *
     * @return Finder The current Finder instance
     */
    public function addAdapter(AdapterInterface $adapter, $priority = 0)
    {
        $this->adapters[$adapter->getName()] = array(
            'adapter'  => $adapter,
            'priority' => $priority,
            'selected' => false,
        );

        return $this->sortAdapters();
    }

    /**
     * Sets the selected adapter to the best one according to the current platform the code is run on.
     *
     * @return Finder The current Finder instance
     */
    public function useBestAdapter()
    {
        $this->resetAdapterSelection();

        return $this->sortAdapters();
    }

    /**
     * Selects the adapter to use.
     *
     * @param string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return Finder The current Finder instance
     */
    public function setAdapter($name)
    {
        if (!isset($this->adapters[$name])) {
            throw new \InvalidArgumentException(sprintf('Adapter "%s" does not exist.', $name));
        }

        $this->resetAdapterSelection();
        $this->adapters[$name]['selected'] = true;

        return $this->sortAdapters();
    }

    /**
     * Removes all adapters registered in the finder.
     *
     * @return Finder The current Finder instance
     */
    public function removeAdapters()
    {
        $this->adapters = array();

        return $this;
    }

    /**
     * Returns registered adapters ordered by priority without extra information.
     *
     * @return AdapterInterface[]
     */
    public function getAdapters()
    {
        return array_values(array_map(function (array $adapter) {
            return $adapter['adapter'];
        }, $this->adapters));
    }

    /**
     * Restricts the matching to directories only.
     *
     * @return Finder The current Finder instance
     *
     * @api
     */
    public function directories()
    {
        $this->mode = Iterator\FileTypeFilterIterator::ONLY_DIRECTORIES;

        return $this;
    }

    /**
     * Restricts the matching to files only.
     *
     * @return Finder The current Finder instance
     *
     * @api
     */
    public function files()
    {
        $this->mode = Iterator\FileTypeFilterIterator::ONLY_FILES;

        return $this;
    }

    /**
     * Adds tests for the directory depth.
     *
     * Usage:
     *
     *   $finder->depth('> 1') // the Finder will start matching at level 1.
     *   $finder->depth('< 3') // the Finder will descend at most 3 levels of directories below the starting point.
     *
     * @param int $level The depth level expression
     *
     * @return Finder The current Finder instance
     *
     * @see Symfony\Component\Finder\Iterator\DepthRangeFilterIterator
     * @see Symfony\Component\Finder\Comparator\NumberComparator
     *
     * @api
     */
    public function depth($level)
    {
        $this->depths[] = new Comparator\NumberComparator($level);

        return $this;
    }

    /**
     * Adds tests for file dates (last modified).
     *
     * The date must be something that strtotime() is able to parse:
     *
     *   $finder->date('since yesterday');
     *   $finder->date('until 2 days ago');
     *   $finder->date('> now - 2 hours');
     *   $finder->date('>= 2005-10-15');
     *
     * @param string $date A date rage string
     *
     * @return Finder The current Finder instance
     *
     * @see strtotime
     * @see Symfony\Component\Finder\Iterator\DateRangeFilterIterator
     * @see Symfony\Component\Finder\Comparator\DateComparator
     *
     * @api
     */
    public function date($date)
    {
        $this->dates[] = new Comparator\DateComparator($date);

        return $this;
    }

    /**
     * Adds rules that files must match.
     *
     * You can use patterns (delimited with / sign), globs or simple strings.
     *
     * $finder->name('*.php')
     * $finder->name('/\.php$/') // same as above
     * $finder->name('test.php')
     *
     * @param string $pattern A pattern (a regexp, a glob, or a string)
     *
     * @return Finder The current Finder instance
     *
     * @see Symfony\Component\Finder\Iterator\FilenameFilterIterator
     *
     * @api
     */
    public function name($pattern)
    {
        $this->names[] = $pattern;

        return $this;
    }

    /**
     * Adds rules that files must not match.
     *
     * @param string $pattern A pattern (a regexp, a glob, or a string)
     *
     * @return Finder The current Finder instance
     *
     * @see Symfony\Component\Finder\Iterator\FilenameFilterIterator
     *
     * @api
     */
    public function notName($pattern)
    {
        $this->notNames[] = $pattern;

        return $this;
    }

    /**
     * Adds tests that file contents must match.
     *
     * Strings or PCRE patterns can be used:
     *
     * $finder->contains('Lorem ipsum')
     * $finder->contains('/Lorem ipsum/i')
     *
     * @param string $pattern A pattern (string or regexp)
     *
     * @return Finder The current Finder instance
     *
     * @see Symfony\Component\Finder\Iterator\FilecontentFilterIterator
     */
    public function contains($pattern)
    {
        $this->contains[] = $pattern;

        return $this;
    }

    /**
     * Adds tests that file contents must not match.
     *
     * Strings or PCRE patterns can be used:
     *
     * $finder->notContains('Lorem ipsum')
     * $finder->notContains('/Lorem ipsum/i')
     *
     * @param string $pattern A pattern (string or regexp)
     *
     * @return Finder The current Finder instance
     *
     * @see Symfony\Component\Finder\Iterator\FilecontentFilterIterator
     */
    public function notContains($pattern)
    {
        $this->notContains[] = $pattern;

        return $this;
    }

    /**
     * Adds rules that filenames must match.
     *
     * You can use patterns (delimited with / sign) or simple strings.
     *
     * $finder->path('some/special/dir')
     * $finder->path('/some\/special\/dir/') // same as above
     *
     * Use only / as dirname separator.
     *
     * @param string $pattern A pattern (a regexp or a string)
     *
     * @return Finder The current Finder instance
     *
     * @see Symfony\Component\Finder\Iterator\FilenameFilterIterator
     */
    public function path($pattern)
    {
        $this->paths[] = $pattern;

        return $this;
    }

    /**
     * Adds rules that filenames must not match.
     *
     * You can use patterns (delimited with / sign) or simple strings.
     *
     * $finder->notPath('some/special/dir')
     * $finder->notPath('/some\/special\/dir/') // same as above
     *
     * Use only / as dirname separator.
     *
     * @param string $pattern A pattern (a regexp or a string)
     *
     * @return Finder The current Finder instance
     *
     * @see Symfony\Component\Finder\Iterator\FilenameFilterIterator
     */
    public function notPath($pattern)
    {
        $this->notPaths[] = $pattern;

        return $this;
    }

    /**
     * Adds tests for file sizes.
     *
     * $finder->size('> 10K');
     * $finder->size('<= 1Ki');
     * $finder->size(4);
     *
     * @param string $size A size range string
     *
     * @return Finder The current Finder instance
     *
     * @see Symfony\Component\Finder\Iterator\SizeRangeFilterIterator
     * @see Symfony\Component\Finder\Comparator\NumberComparator
     *
     * @api
     */
    public function size($size)
    {
        $this->sizes[] = new Comparator\NumberComparator($size);

        return $this;
    }

    /**
     * Excludes directories.
     *
     * @param string|array $dirs A directory path or an array of directories
     *
     * @return Finder The current Finder instance
     *
     * @see Symfony\Component\Finder\Iterator\ExcludeDirectoryFilterIterator
     *
     * @api
     */
    public function exclude($dirs)
    {
        $this->exclude = array_merge($this->exclude, (array) $dirs);

        return $this;
    }

    /**
     * Excludes "hidden" directories and files (starting with a dot).
     *
     * @param bool    $ignoreDotFiles Whether to exclude "hidden" files or not
     *
     * @return Finder The current Finder instance
     *
     * @see Symfony\Component\Finder\Iterator\ExcludeDirectoryFilterIterator
     *
     * @api
     */
    public function ignoreDotFiles($ignoreDotFiles)
    {
        if ($ignoreDotFiles) {
            $this->ignore = $this->ignore | static::IGNORE_DOT_FILES;
        } else {
            $this->ignore = $this->ignore & ~static::IGNORE_DOT_FILES;
        }

        return $this;
    }

    /**
     * Forces the finder to ignore version control directories.
     *
     * @param bool    $ignoreVCS Whether to exclude VCS files or not
     *
     * @return Finder The current Finder instance
     *
     * @see Symfony\Component\Finder\Iterator\ExcludeDirectoryFilterIterator
     *
     * @api
     */
    public function ignoreVCS($ignoreVCS)
    {
        if ($ignoreVCS) {
            $this->ignore = $this->ignore | static::IGNORE_VCS_FILES;
        } else {
            $this->ignore = $this->ignore & ~static::IGNORE_VCS_FILES;
        }

        return $this;
    }

    /**
     * Adds VCS patterns.
     *
     * @see ignoreVCS
     *
     * @param string|string[] $pattern VCS patterns to ignore
     */
    public static function addVCSPattern($pattern)
    {
        foreach ((array) $pattern as $p) {
            self::$vcsPatterns[] = $p;
        }

        self::$vcsPatterns = array_unique(self::$vcsPatterns);
    }

    /**
     * Sorts files and directories by an anonymous function.
     *
     * The anonymous function receives two \SplFileInfo instances to compare.
     *
     * This can be slow as all the matching files and directories must be retrieved for comparison.
     *
     * @param \Closure $closure An anonymous function
     *
     * @return Finder The current Finder instance
     *
     * @see Symfony\Component\Finder\Iterator\SortableIterator
     *
     * @api
     */
    public function sort(\Closure $closure)
    {
        $this->sort = $closure;

        return $this;
    }

    /**
     * Sorts files and directories by name.
     *
     * This can be slow as all the matching files and directories must be retrieved for comparison.
     *
     * @return Finder The current Finder instance
     *
     * @see Symfony\Component\Finder\Iterator\SortableIterator
     *
     * @api
     */
    public function sortByName()
    {
        $this->sort = Iterator\SortableIterator::SORT_BY_NAME;

        return $this;
    }

    /**
     * Sorts files and directories by type (directories before files), then by name.
     *
     * This can be slow as all the matching files and directories must be retrieved for comparison.
     *
     * @return Finder The current Finder instance
     *
     * @see Symfony\Component\Finder\Iterator\SortableIterator
     *
     * @api
     */
    public function sortByType()
    {
        $this->sort = Iterator\SortableIterator::SORT_BY_TYPE;

        return $this;
    }

    /**
     * Sorts files and directories by the last accessed time.
     *
     * This is the time that the file was last accessed, read or written to.
     *
     * This can be slow as all the matching files and directories must be retrieved for comparison.
     *
     * @return Finder The current Finder instance
     *
     * @see Symfony\Component\Finder\Iterator\SortableIterator
     *
     * @api
     */
    public function sortByAccessedTime()
    {
        $this->sort = Iterator\SortableIterator::SORT_BY_ACCESSED_TIME;

        return $this;
    }

    /**
     * Sorts files and directories by the last inode changed time.
     *
     * This is the time that the inode information was last modified (permissions, owner, group or other metadata).
     *
     * On Windows, since inode is not available, changed time is actually the file creation time.
     *
     * This can be slow as all the matching files and directories must be retrieved for comparison.
     *
     * @return Finder The current Finder instance
     *
     * @see Symfony\Component\Finder\Iterator\SortableIterator
     *
     * @api
     */
    public function sortByChangedTime()
    {
        $this->sort = Iterator\SortableIterator::SORT_BY_CHANGED_TIME;

        return $this;
    }

    /**
     * Sorts files and directories by the last modified time.
     *
     * This is the last time the actual contents of the file were last modified.
     *
     * This can be slow as all the matching files and directories must be retrieved for comparison.
     *
     * @return Finder The current Finder instance
     *
     * @see Symfony\Component\Finder\Iterator\SortableIterator
     *
     * @api
     */
    public function sortByModifiedTime()
    {
        $this->sort = Iterator\SortableIterator::SORT_BY_MODIFIED_TIME;

        return $this;
    }

    /**
     * Filters the iterator with an anonymous function.
     *
     * The anonymous function receives a \SplFileInfo and must return false
     * to remove files.
     *
     * @param \Closure $closure An anonymous function
     *
     * @return Finder The current Finder instance
     *
     * @see Symfony\Component\Finder\Iterator\CustomFilterIterator
     *
     * @api
     */
    public function filter(\Closure $closure)
    {
        $this->filters[] = $closure;

        return $this;
    }

    /**
     * Forces the following of symlinks.
     *
     * @return Finder The current Finder instance
     *
     * @api
     */
    public function followLinks()
    {
        $this->followLinks = true;

        return $this;
    }

    /**
     * Tells finder to ignore unreadable directories.
     *
     * By default, scanning unreadable directories content throws an AccessDeniedException.
     *
     * @param bool    $ignore
     *
     * @return Finder The current Finder instance
     */
    public function ignoreUnreadableDirs($ignore = true)
    {
        $this->ignoreUnreadableDirs = (bool) $ignore;

        return $this;
    }

    /**
     * Searches files and directories which match defined rules.
     *
     * @param string|array $dirs A directory path or an array of directories
     *
     * @return Finder The current Finder instance
     *
     * @throws \InvalidArgumentException if one of the directories does not exist
     *
     * @api
     */
    public function in($dirs)
    {
        $resolvedDirs = array();

        foreach ((array) $dirs as $dir) {
            if (is_dir($dir)) {
                $resolvedDirs[] = $dir;
            } elseif ($glob = glob($dir, GLOB_ONLYDIR)) {
                $resolvedDirs = array_merge($resolvedDirs, $glob);
            } else {
                throw new \InvalidArgumentException(sprintf('The "%s" directory does not exist.', $dir));
            }
        }

        $this->dirs = array_merge($this->dirs, $resolvedDirs);

        return $this;
    }

    /**
     * Returns an Iterator for the current Finder configuration.
     *
     * This method implements the IteratorAggregate interface.
     *
     * @return \Iterator An iterator
     *
     * @throws \LogicException if the in() method has not been called
     */
    public function getIterator()
    {
        if (0 === count($this->dirs) && 0 === count($this->iterators)) {
            throw new \LogicException('You must call one of in() or append() methods before iterating over a Finder.');
        }

        if (1 === count($this->dirs) && 0 === count($this->iterators)) {
            return $this->searchInDirectory($this->dirs[0]);
        }

        $iterator = new \AppendIterator();
        foreach ($this->dirs as $dir) {
            $iterator->append($this->searchInDirectory($dir));
        }

        foreach ($this->iterators as $it) {
            $iterator->append($it);
        }

        return $iterator;
    }

    /**
     * Appends an existing set of files/directories to the finder.
     *
     * The set can be another Finder, an Iterator, an IteratorAggregate, or even a plain array.
     *
     * @param mixed $iterator
     *
     * @return Finder The finder
     *
     * @throws \InvalidArgumentException When the given argument is not iterable.
     */
    public function append($iterator)
    {
        if ($iterator instanceof \IteratorAggregate) {
            $this->iterators[] = $iterator->getIterator();
        } elseif ($iterator instanceof \Iterator) {
            $this->iterators[] = $iterator;
        } elseif ($iterator instanceof \Traversable || is_array($iterator)) {
            $it = new \ArrayIterator();
            foreach ($iterator as $file) {
                $it->append($file instanceof \SplFileInfo ? $file : new \SplFileInfo($file));
            }
            $this->iterators[] = $it;
        } else {
            throw new \InvalidArgumentException('Finder::append() method wrong argument type.');
        }

        return $this;
    }

    /**
     * Counts all the results collected by the iterators.
     *
     * @return int
     */
    public function count()
    {
        return iterator_count($this->getIterator());
    }

    /**
     * @return Finder The current Finder instance
     */
    private function sortAdapters()
    {
        uasort($this->adapters, function (array $a, array $b) {
            if ($a['selected'] || $b['selected']) {
                return $a['selected'] ? -1 : 1;
            }

            return $a['priority'] > $b['priority'] ? -1 : 1;
        });

        return $this;
    }

    /**
     * @param $dir
     *
     * @return \Iterator
     *
     * @throws \RuntimeException When none of the adapters are supported
     */
    private function searchInDirectory($dir)
    {
        if (static::IGNORE_VCS_FILES === (static::IGNORE_VCS_FILES & $this->ignore)) {
            $this->exclude = array_merge($this->exclude, self::$vcsPatterns);
        }

        if (static::IGNORE_DOT_FILES === (static::IGNORE_DOT_FILES & $this->ignore)) {
            $this->notPaths[] = '#(^|/)\..+(/|$)#';
        }

        foreach ($this->adapters as $adapter) {
            if ($adapter['adapter']->isSupported()) {
                try {
                    return $this
                        ->buildAdapter($adapter['adapter'])
                        ->searchInDirectory($dir);
                } catch (ExceptionInterface $e) {
                }
            }
        }

        throw new \RuntimeException('No supported adapter found.');
    }

    /**
     * @param AdapterInterface $adapter
     *
     * @return AdapterInterface
     */
    private function buildAdapter(AdapterInterface $adapter)
    {
        return $adapter
            ->setFollowLinks($this->followLinks)
            ->setDepths($this->depths)
            ->setMode($this->mode)
            ->setExclude($this->exclude)
            ->setNames($this->names)
            ->setNotNames($this->notNames)
            ->setContains($this->contains)
            ->setNotContains($this->notContains)
            ->setSizes($this->sizes)
            ->setDates($this->dates)
            ->setFilters($this->filters)
            ->setSort($this->sort)
            ->setPath($this->paths)
            ->setNotPath($this->notPaths)
            ->ignoreUnreadableDirs($this->ignoreUnreadableDirs);
    }

    /**
     * Unselects all adapters.
     */
    private function resetAdapterSelection()
    {
        $this->adapters = array_map(function (array $properties) {
            $properties['selected'] = false;

            return $properties;
        }, $this->adapters);
    }
}
