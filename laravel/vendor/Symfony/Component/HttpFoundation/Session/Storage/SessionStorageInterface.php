<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Session\Storage;

use Symfony\Component\HttpFoundation\Session\SessionBagInterface;

/**
 * StorageInterface.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Drak <drak@zikula.org>
 *
 * @api
 */
interface SessionStorageInterface
{
    /**
     * Starts the session.
     *
     * @throws \RuntimeException If something goes wrong starting the session.
     *
     * @return boolean True if started.
     *
     * @api
     */
    function start();

    /**
     * Returns the session ID
     *
     * @return string The session ID or empty.
     *
     * @api
     */
    function getId();

    /**
     * Sets the session ID
     *
     * @param string $id
     *
     * @api
     */
    function setId($id);

    /**
     * Returns the session name
     *
     * @return mixed The session name.
     *
     * @api
     */
    function getName();

    /**
     * Sets the session name
     *
     * @param string $name
     *
     * @api
     */
    function setName($name);

    /**
     * Regenerates id that represents this storage.
     *
     * This method must invoke session_regenerate_id($destroy) unless
     * this interface is used for a storage object designed for unit
     * or functional testing where a real PHP session would interfere
     * with testing.
     *
     * Note regenerate+destroy should not clear the session data in memory
     * only delete the session data from persistent storage.
     *
     * @param  Boolean $destroy Destroy session when regenerating?
     *
     * @return Boolean True if session regenerated, false if error
     *
     * @throws \RuntimeException If an error occurs while regenerating this storage
     *
     * @api
     */
    function regenerate($destroy = false);

    /**
     * Force the session to be saved and closed.
     *
     * This method must invoke session_write_close() unless this interface is
     * used for a storage object design for unit or functional testing where
     * a real PHP session would interfere with testing, in which case it
     * it should actually persist the session data if required.
     */
    function save();

    /**
     * Clear all session data in memory.
     */
    function clear();

    /**
     * Gets a SessionBagInterface by name.
     *
     * @param string $name
     *
     * @return SessionBagInterface
     *
     * @throws \InvalidArgumentException If the bag does not exist
     */
    function getBag($name);

    /**
     * Registers a SessionBagInterface for use.
     *
     * @param SessionBagInterface $bag
     */
    function registerBag(SessionBagInterface $bag);
}
