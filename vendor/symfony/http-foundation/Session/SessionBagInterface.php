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
     * Gets this bag's name.
     */
    public function getName(): string;

    /**
     * Initializes the Bag.
     */
    public function initialize(array &$array): void;

    /**
     * Gets the storage key for this bag.
     */
    public function getStorageKey(): string;

    /**
     * Clears out data from bag.
     *
     * @return mixed Whatever data was contained
     */
    public function clear(): mixed;
}
