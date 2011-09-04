<?php namespace Laravel;

abstract class Controller extends Resolver {

	/**
	 * A stub method that will be called before every request to the controller.
	 *
	 * If a value is returned by the method, it will be halt the request cycle
	 * and will be considered the response to the request.
	 *
	 * @return mixed
	 */
	public function before() {}

	/**
	 * Magic Method to handle calls to undefined functions on the controller.
	 *
	 * By default, the 404 response will be returned for an calls to undefined
	 * methods on the controller. However, this method may also be overridden
	 * and used as a pseudo-router by the controller.
	 */
	public function __call($method, $parameters)
	{
		return $this->response->error('404');
	}

}