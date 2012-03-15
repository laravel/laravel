<?php namespace Jackzz\Events;

class RoleCreated {

	public function __construct($attributes)
	{
		$this->attributes = $attributes;
	}

}