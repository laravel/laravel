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
     * @return bool    True if started.
     *
     * @api
     */
    public function start();

    /**
     * Checks if the session is started.
     *
     * @return bool    True if started, false otherwise.
     */
    public function isStarted();

    /**
     * Returns the session ID
     *
     * @return string The session ID or empty.
     *
     * @api
     */
    public function getId();

    /**
     * Sets the session ID
     *
     * @param string $id
     *
     * @api
     */
    public function setId($id);

    /**
     * Returns the session name
     *
     * @return mixed The session name.
     *
     * @api
     */
    public function getName();

    /**
     * Sets the session name
     *
     * @param string $name
     *
     * @api
     */
    public function setName($name);

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
     * @param bool    $destroy  Destroy session when regenerating?
     * @param int     $lifetime Sets the cookie lifetime for the session cookie. A null value
     *                          will leave the system settings unchanged, 0 sets the cookie
     *                          to expire with browser session. Time is in seconds, and is
     *                          not a Unix timestamp.
     *
     * @return bool    True if session regenerated, false if error
     *
     * @throws \RuntimeException If an error occurs while regenerating this storage
     *
     * @api
     */
    public function regenerate($destroy = false, $lifetime = null);

    /**
     * Force the session to be saved and closed.
     *
     * This method must invoke session_write_close() unless this interface is
     * used for a storage object design for unit or functional testing where
     * a real PHP session would interfere with testing, in which case it
     * it should actually persist the session data if required.
     *
     * @throws \RuntimeException If the session is saved without being started, or if the session
     *                           is already closed.
     */
    public function save();

    /**
     * Clear all session data in memory.
     */
    public function clear();

    /**
     * Gets a SessionBagInterface by name.
     *
     * @param string $name
     *
     * @return SessionBagInterface
     *
     * @throws \InvalidArgumentException If the bag does not exist
     */
    public function getBag($name);

    /**
     * Registers a SessionBagInterface for use.
     *
     * @param SessionBagInterface $bag
     */
    public function registerBag(SessionBagInterface $bag);

    /**
     * @return MetadataBag
     */
    public function getMetadataBag();
}
