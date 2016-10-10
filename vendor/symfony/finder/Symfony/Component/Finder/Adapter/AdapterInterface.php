<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Finder\Adapter;

/**
 * @author Jean-François Simon <contact@jfsimon.fr>
 */
interface AdapterInterface
{
    /**
     * @param bool    $followLinks
     *
     * @return AdapterInterface Current instance
     */
    public function setFollowLinks($followLinks);

    /**
     * @param int     $mode
     *
     * @return AdapterInterface Current instance
     */
    public function setMode($mode);

    /**
     * @param array $exclude
     *
     * @return AdapterInterface Current instance
     */
    public function setExclude(array $exclude);

    /**
     * @param array $depths
     *
     * @return AdapterInterface Current instance
     */
    public function setDepths(array $depths);

    /**
     * @param array $names
     *
     * @return AdapterInterface Current instance
     */
    public function setNames(array $names);

    /**
     * @param array $notNames
     *
     * @return AdapterInterface Current instance
     */
    public function setNotNames(array $notNames);

    /**
     * @param array $contains
     *
     * @return AdapterInterface Current instance
     */
    public function setContains(array $contains);

    /**
     * @param array $notContains
     *
     * @return AdapterInterface Current instance
     */
    public function setNotContains(array $notContains);

    /**
     * @param array $sizes
     *
     * @return AdapterInterface Current instance
     */
    public function setSizes(array $sizes);

    /**
     * @param array $dates
     *
     * @return AdapterInterface Current instance
     */
    public function setDates(array $dates);

    /**
     * @param array $filters
     *
     * @return AdapterInterface Current instance
     */
    public function setFilters(array $filters);

    /**
     * @param \Closure|int     $sort
     *
     * @return AdapterInterface Current instance
     */
    public function setSort($sort);

    /**
     * @param array $paths
     *
     * @return AdapterInterface Current instance
     */
    public function setPath(array $paths);

    /**
     * @param array $notPaths
     *
     * @return AdapterInterface Current instance
     */
    public function setNotPath(array $notPaths);

    /**
     * @param bool    $ignore
     *
     * @return AdapterInterface Current instance
     */
    public function ignoreUnreadableDirs($ignore = true);

    /**
     * @param string $dir
     *
     * @return \Iterator Result iterator
     */
    public function searchInDirectory($dir);

    /**
     * Tests adapter support for current platform.
     *
     * @return bool
     */
    public function isSupported();

    /**
     * Returns adapter name.
     *
     * @return string
     */
    public function getName();
}
