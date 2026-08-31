<?php

namespace Illuminate\Contracts\Session;

interface Session
{
    /**
     * Get the name of the session.
     *
     * @return string
     */
    public function getName();

    /**
     * Set the name of the session.
     *
     * @param  string  $name
     * @return void
     */
    public function setName($name);

    /**
     * Get the current session ID.
     *
     * @return string
     */
    public function getId();

    /**
     * Set the session ID.
     *
     * @param  string  $id
     * @return void
     */
    public function setId($id);

    /**
     * Start the session, reading the data from a handler.
     *
     * @return bool
     */
    public function start();

    /**
     * Save the session data to storage.
     *
     * @return void
     */
    public function save();

    /**
     * Get all of the session data.
     *
     * @return array
     */
    public function all();

    /**
     * Checks if a key exists.
     *
     * @param  string|array  $key
     * @return bool
     */
    public function exists($key);

    /**
     * Checks if a key is present and not null.
     *
     * @param  string|array  $key
     * @return bool
     */
    public function has($key);

    /**
     * Get an item from the session.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Get the value of a given key and then forget it.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function pull($key, $default = null);

    /**
     * Put a key / value pair or array of key / value pairs in the session.
     *
     * @param  string|array  $key
     * @param  mixed  $value
     * @return void
     */
    public function put($key, $value = null);

    /**
     * Flash a key / value pair to the session.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function flash(string $key, $value = true);

    /**
     * Get the CSRF token value.
     *
     * @return string
     */
    public function token();

    /**
     * Regenerate the CSRF token value.
     *
     * @return void
     */
    public function regenerateToken();

    /**
     * Remove an item from the session, returning its value.
     *
     * @param  string  $key
     * @return mixed
     */
    public function remove($key);

    /**
     * Remove one or many items from the session.
     *
     * @param  string|array  $keys
     * @return void
     */
    public function forget($keys);

    /**
     * Remove all of the items from the session.
     *
     * @return void
     */
    public function flush();

    /**
     * Flush the session data and regenerate the ID.
     *
     * @return bool
     */
    public function invalidate();

    /**
     * Generate a new session identifier.
     *
     * @param  bool  $destroy
     * @return bool
     */
    public function regenerate($destroy = false);

    /**
     * Generate a new session ID for the session.
     *
     * @param  bool  $destroy
     * @return bool
     */
    public function migrate($destroy = false);

    /**
     * Determine if the session has been started.
     *
     * @return bool
     */
    public function isStarted();

    /**
     * Get the previous URL from the session.
     *
     * @return string|null
     */
    public function previousUrl();

    /**
     * Set the "previous" URL in the session.
     *
     * @param  string  $url
     * @return void
     */
    public function setPreviousUrl($url);

    /**
     * Get the session handler instance.
     *
     * @return \SessionHandlerInterface
     */
    public function getHandler();

    /**
     * Determine if the session handler needs a request.
     *
     * @return bool
     */
    public function handlerNeedsRequest();

    /**
     * Set the request on the handler instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function setRequestOnHandler($request);
}
