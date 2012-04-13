<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Session;

/**
 * Session Bag store.
 *
 * @author Drak <drak@zikula.org>
 */
interface SessionBagInterface
{
    /**
     * Gets this bag's name
     *
     * @return string
     */
    function getName();

    /**
     * Initializes the Bag
     *
     * @param array $array
     */
    function initialize(array &$array);

    /**
     * Gets the storage key for this bag.
     *
     * @return string
     */
    function getStorageKey();

    /**
     * Clears out data from bag.
     *
     * @return mixed Whatever data was contained.
     */
    function clear();
}
