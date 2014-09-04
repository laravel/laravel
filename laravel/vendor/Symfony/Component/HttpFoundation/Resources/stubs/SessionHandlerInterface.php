<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * SessionHandlerInterface
 *
 * Provides forward compatibility with PHP 5.4
 *
 * Extensive documentation can be found at php.net, see links:
 *
 * @see http://php.net/sessionhandlerinterface
 * @see http://php.net/session.customhandler
 * @see http://php.net/session-set-save-handler
 *
 * @author Drak <drak@zikula.org>
 */
interface SessionHandlerInterface
{
    /**
     * Open session.
     *
     * @see http://php.net/sessionhandlerinterface.open
     *
     * @param string $savePath    Save path.
     * @param string $sessionName Session Name.
     *
     * @throws \RuntimeException If something goes wrong starting the session.
     *
     * @return boolean
     */
    public function open($savePath, $sessionName);

    /**
     * Close session.
     *
     * @see http://php.net/sessionhandlerinterface.close
     *
     * @return boolean
     */
    public function close();

    /**
     * Read session.
     *
     * @param string $sessionId
     *
     * @see http://php.net/sessionhandlerinterface.read
     *
     * @throws \RuntimeException On fatal error but not "record not found".
     *
     * @return string String as stored in persistent storage or empty string in all other cases.
     */
    public function read($sessionId);

    /**
     * Commit session to storage.
     *
     * @see http://php.net/sessionhandlerinterface.write
     *
     * @param string $sessionId Session ID.
     * @param string $data      Session serialized data to save.
     *
     * @return boolean
     */
    public function write($sessionId, $data);

    /**
     * Destroys this session.
     *
     * @see http://php.net/sessionhandlerinterface.destroy
     *
     * @param string $sessionId Session ID.
     *
     * @throws \RuntimeException On fatal error.
     *
     * @return boolean
     */
    public function destroy($sessionId);

    /**
     * Garbage collection for storage.
     *
     * @see http://php.net/sessionhandlerinterface.gc
     *
     * @param integer $lifetime Max lifetime in seconds to keep sessions stored.
     *
     * @throws \RuntimeException On fatal error.
     *
     * @return boolean
     */
    public function gc($lifetime);
}
