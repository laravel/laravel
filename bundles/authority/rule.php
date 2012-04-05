<?php namespace Authority;

/**
* Authority
*
* Authority is an authorization library for CodeIgniter 2+ and PHPActiveRecord
* This library is inspired by, and largely based off, Ryan Bates' CanCan gem
* for Ruby on Rails. It is not a 1:1 port, but the essentials are available.
* Please check out his work at http://github.com/ryanb/cancan/
*
* @package Authority
* @version 0.0.3
* @author Matthew Machuga
* @license MIT License
* @copyright 2011 Matthew Machuga
* @link http://github.com/machuga
*
**/

class Rule {

	protected $_allowed     = false;
	protected $_resource    = null;
	protected $_action      = null;
	protected $_callback    = null;

	public function __construct($allowed, $action, $resource, \Closure $callback = null)
	{
		$this->_allowed     = $allowed;
		$this->_action      = $action;
		$this->_resource    = $resource;
		$this->_callback    = $callback;
	}

	public function allowed()
	{
		return $this->_allowed;
	}

	public function matches_action($action)
	{
		return is_array($action)    ? in_array($this->_action, $action) 
									: $this->_action === $action;
	}

	public function matches_resource($resource)
	{
		$resource = is_object($resource) ? get_class($resource) : $resource;
		return $this->_resource === $resource || $this->_resource === 'all';
	}

	public function relevant($action, $resource)
	{
		return $this->matches_action($action) && $this->matches_resource($resource);
	}

	public function callback($resource)
	{
		if (isset($this->_callback) && is_string($resource)) {
			return false;
		}
		return (isset($this->_callback)) ? $this->_callback($resource) : true;
	}

	// Allow callbacks to be called
	public function __call($method, $args)
	{
		return (isset($this->$method)) ? call_user_func_array($this->$method, $args) : true;
	}

}