<?php namespace Jackzz\Events;

class RolesAssignedToAccount {
	
	public function __construct($attributes)
	{
		$this->attributes = $attributes;
	}

}