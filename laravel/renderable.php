<?php namespace Laravel;

interface Renderable {

	/**
	 * Get the evaluated string contents of the object.
	 *
	 * @return string
	 */
	public function render();

}