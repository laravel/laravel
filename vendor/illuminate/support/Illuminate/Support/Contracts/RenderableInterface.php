<?php namespace Illuminate\Support\Contracts;

interface RenderableInterface {

	/**
	 * Get the evaluated contents of the object.
	 *
	 * @return string
	 */
	public function render();

}
