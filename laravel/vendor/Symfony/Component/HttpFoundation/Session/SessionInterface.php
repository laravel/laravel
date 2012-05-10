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
 * Interface for the session.
 *
 * @author Drak <drak@zikula.org>
 */
interface SessionInterface
{
    /**
     * Starts the session storage.
     *
     * @return Boolean True if session started.
     *
     * @throws \RuntimeException If session fails to start.
     *
     * @api
     */
    function start();

    /**
     * Returns the session ID.
     *
     * @return string The session ID.
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
     * Returns the session name.
     *
     * @return mixed The session name.
     *
     * @api
     */
    function getName();

    /**
     * Sets the session name.
     *
     * @param string $name
     *
     * @api
     */
    function setName($name);

    /**
     * Invalidates the current session.
     *
     * Clears all session attributes and flashes and regenerates the
     * session and deletes the old session from persistence.
     *
     * @return Boolean True if session invalidated, false if error.
     *
     * @api
     */
    function invalidate();

    /**
     * Migrates the current session to a new session id while maintaining all
     * session attributes.
     *
     * @param Boolean $destroy Whether to delete the old session or leave it to garbage collection.
     *
     * @return Boolean True if session migrated, false if error.
     *
     * @api
     */
    function migrate($destroy = false);

    /**
     * Force the session to be saved and closed.
     *
     * This method is generally not required for real sessions as
     * the session will be automatically saved at the end of
     * code execution.
     */
    function save();

    /**
     * Checks if an attribute is defined.
     *
     * @param string $name The attribute name
     *
     * @return Boolean true if the attribute is defined, false otherwise
     *
     * @api
     */
    function has($name);

    /**
     * Returns an attribute.
     *
     * @param string $name    The attribute name
     * @param mixed  $default The default value if not found.
     *
     * @return mixed
     *
     * @api
     */
    function get($name, $default = null);

    /**
     * Sets an attribute.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @api
     */
    function set($name, $value);

    /**
     * Returns attributes.
     *
     * @return array Attributes
     *
     * @api
     */
    function all();

    /**
     * Sets attributes.
     *
     * @param array $attributes Attributes
     */
    function replace(array $attributes);

    /**
     * Removes an attribute.
     *
     * @param string $name
     *
     * @return mixed The removed value
     *
     * @api
     */
    function remove($name);

    /**
     * Clears all attributes.
     *
     * @api
     */
    function clear();
}
