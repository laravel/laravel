<?php namespace System\Session;

interface Driver {

	/**
	 * Load a session by ID.
	 *
	 * @param  string  $id
	 * @return array
	 */
	public function load($id);

	/**
	 * Save a session.
	 *
	 * @param  array  $session
	 * @return void
	 */
	public function save($session);

	/**
	 * Delete a session by ID.
	 *
	 * @param  string  $id
	 * @return void
	 */
	public function delete($id);

	/**
	 * Delete all expired sessions.
	 *
	 * @param  int   $expiration
	 * @return void
	 */
	public function sweep($expiration);

}