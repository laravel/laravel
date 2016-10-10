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
 * SessionHandlerInterface for PHP < 5.4
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
     * Re-initializes existing session, or creates a new one.
     *
     * @see http://php.net/sessionhandlerinterface.open
     *
     * @param string $savePath    Save path
     * @param string $sessionName Session name, see http://php.net/function.session-name.php
     *
     * @return bool true on success, false on failure
     */
    public function open($savePath, $sessionName);

    /**
     * Closes the current session.
     *
     * @see http://php.net/sessionhandlerinterface.close
     *
     * @return bool true on success, false on failure
     */
    public function close();

    /**
     * Reads the session data.
     *
     * @see http://php.net/sessionhandlerinterface.read
     *
     * @param string $sessionId Session ID, see http://php.net/function.session-id
     *
     * @return string Same session data as passed in write() or empty string when non-existent or on failure
     */
    public function read($sessionId);

    /**
     * Writes the session data to the storage.
     *
     * @see http://php.net/sessionhandlerinterface.write
     *
     * @param string $sessionId Session ID , see http://php.net/function.session-id
     * @param string $data      Serialized session data to save
     *
     * @return bool true on success, false on failure
     */
    public function write($sessionId, $data);

    /**
     * Destroys a session.
     *
     * @see http://php.net/sessionhandlerinterface.destroy
     *
     * @param string $sessionId Session ID, see http://php.net/function.session-id
     *
     * @return bool true on success, false on failure
     */
    public function destroy($sessionId);

    /**
     * Cleans up expired sessions (garbage collection).
     *
     * @see http://php.net/sessionhandlerinterface.gc
     *
     * @param string|int $maxlifetime Sessions that have not updated for the last maxlifetime seconds will be removed
     *
     * @return bool true on success, false on failure
     */
    public function gc($maxlifetime);
}
